const puppeteer = require("puppeteer");
const path = require('path');

const url = "https://gshow.globo.com/realities/bbb";
const url_ocr = "https://www.onlineocr.net/pt/";

// Função para adicionar um atraso.
async function sleep (seg) {
  return new Promise((resolve,reject) => {
    setTimeout(function(){
      resolve();
    }, seg*1000);
  });
};

async function convertImageToText(imagePath) {
  
  // Inicia o navegador.
  const browser = await puppeteer.launch({
    headless: false,
  });

  // Abre nova aba na constante 'page'.
  const page = await browser.newPage();
  
  try {
    
    // Acessar a URL na aba 'page'. O parâmetro timeout: 6000O => limita processamento da página em até 60s,
    // se passar gera o erro: 'Navigation timeout ... exceeded'.
    await page.goto(url_ocr,{ waitUntil: 'networkidle2', timeout: 60000});
    
    // Esperar.
    //await sleep(2);
    
    // Aguardar o elemento.
    await page.waitForSelector('#fileupload', { timeout: 10000 });

    // Seleciona o arquivo para upload.
    const filePath = path.relative(process.cwd(), imagePath);
    const inputUploadHandle = await page.$('#fileupload');
    await inputUploadHandle.uploadFile(filePath);
    
    // Esperar..
    await sleep(3);
    
    // Clicar em enviar.
    await page.click('#MainContent_btnOCRConvert');

    // Esperar.
    await sleep(3);

    try {
      // Define o elemento que indica a restrição.
      const restrictionSelector = '#MainContent_PanelAlert';
      // Verifica o elemento de restrição está presente
      const isRestrictionVisible = await page.$(restrictionSelector) !== null;
      if (isRestrictionVisible) {
          const restrictionMessage = await page.$eval(restrictionSelector, el => el.textContent);
          console.log(`Excesso de tentativas: ${restrictionMessage}`);
          await browser.close();
          return restrictionMessage; // Encerra o script após capturar a mensagem de restrição
      }

      // Aguardar o elemento.
      await page.waitForSelector('#MainContent_txtOCRResultText', { timeout: 16000 });
      
      // Obter o retorno no elemento '#MainContent_txtOCRResultText'.
      result = await page.$eval('#MainContent_txtOCRResultText', el => el.value);
        
      // Fecha navegador.
      await browser.close();

      // Retorna o resultado da raspagem.
      return result;

    } catch (error) {
      console.error('Erro na espera do elemento: ', error.message);
      console.log('Erro na espera do elemento: ' + error.message);

      // Fecha navegador
      await browser.close();
      
      return error;
    }

  } catch (error) {
    console.error('Erro no processamento da página: ', error.message);
    console.log('Erro no processamento da página: ' + error.message);

    // Fecha navegador
    await browser.close();
    
    return `Erro ao processar: ${error.message}`;
  }
}

const url_show = "https://gshow.globo.com/realities/bbb";
async function getPosts() {
  const browser = await puppeteer.launch({
    headless: false,
  });

  const page = await browser.newPage();
  await page.goto(url_show);

  //page.on("console", (msg) => console.log("PAGE LOG:", msg.text()));

  const posts = await page.evaluate(async () => {

    const posts = Array.from(document.querySelectorAll(".post-item"));

    const data = posts.map((post) => ({
      url: post.querySelector(".post-materia-text")?.getAttribute("href"),
      title: post.querySelector(".post-materia-text__title")?.textContent,
      description: post.querySelector(".post-materia-text__description")
        ?.textContent,
    }));

    return data.filter((post) => post.url);
  });

  await browser.close();
  console.log(posts);
}

const imagePath = process.argv[2];
convertImageToText(imagePath).then(console.log);
//getPosts();