const puppeteer = require("puppeteer");
const path = require('path');

const url = "https://gshow.globo.com/realities/bbb";
const url_ocr = "https://www.onlineocr.net/pt/";

async function main() {
  const browser = await puppeteer.launch({
    headless: false,
  });

  const page = await browser.newPage();
  await page.goto(url);

  //page.on("console", (msg) => console.log("PAGE LOG:", msg.text()));

  const posts = await page.evaluate(async () => {
    await new Promise((resolve) => {
      const distance = 100;
      let scrolledAmount = 0;

      const timer = setInterval(() => {
        window.scrollBy(0, distance);
        scrolledAmount += distance;

        if (scrolledAmount >= document.body.scrollHeight) {
          clearInterval(timer);
          resolve();
        }
      }, 100);
    });

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
async function test() {
  const browser = await puppeteer.launch({
    headless: false,
  });

  const page = await browser.newPage();
  await page.goto(url);

  //page.on("console", (msg) => console.log("PAGE LOG:", msg.text()));

  const posts = await page.evaluate(async () => {
    // await new Promise((resolve) => {
    //   const distance = 100;
    //   let scrolledAmount = 0;

    //   const timer = setInterval(() => {
    //     window.scrollBy(0, distance);
    //     scrolledAmount += distance;

    //     if (scrolledAmount >= document.body.scrollHeight) {
    //       clearInterval(timer);
    //       resolve();
    //     }
    //   }, 100);
    // });

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


async function ocr() {
  
  // Função para adicionar um atraso
  const delay = (time) => {
    return new Promise(resolve => setTimeout(resolve, time));
  };

  // Inicia o navegador e abre uma nova página
  //console.log('Iniciando o Puppeteer...');
  const browser = await puppeteer.launch({
    headless: false,
  });

  //console.log('Navegador iniciado, acessando o site...');
  const page = await browser.newPage();
  await page.goto(url_ocr,{ waitUntil: 'networkidle2', timeout: 60000});
  //console.log('Site acessado...');

  // Adiciona um tempo de espera antes de selecionar o arquivo
  await delay(3000); // Espera por 3 segundos

  await page.waitForSelector('#fileupload', { timeout: 100000 });
  //await page.waitForTimeout(6000); // Atraso de 2 segundo
  //console.log('Aguardando elemento #fileupload ...');
  
  
  //console.log('Seleciona o arquivo ...');
  // Seleciona o arquivo para upload
  const filePath = path.relative(process.cwd(), 'F:/00_SYNC/AppLaravel/23_09/web-scraping/storage/app/images/001-100.jpg'); // Substitua pelo caminho real do arquivo
  const inputUploadHandle = await page.$('#fileupload');
  await inputUploadHandle.uploadFile(filePath);
  
  // Adiciona um tempo de espera antes de clicar em enviar.
  await delay(4000); // Espera por 12 segundos

  //console.log('Acionando o botão de conversão......');
  await page.click('#MainContent_btnOCRConvert'); // Substitua pelo seletor real do botão de envio

  //console.log('Esperando o resultado...');
  await delay(15000); // Espera por 25 segundos

  // Esperar o resultado e extrair o texto
    try {
        await page.waitForSelector('#MainContent_txtOCRResultText', { timeout: 20000 });
        const result = await page.$eval('#MainContent_txtOCRResultText', el => el.value);
        //console.log('Texto extraído:', result);
        await browser.close();
        return result;
    } catch (error) {
        console.error('Erro ao extrair o texto:', error);
        await browser.close();
        return null;
    }


  page.on("console", (msg) => console.log("PAGE LOG:", msg.text()));

  //await page.waitForTimeout(6000); // Atraso de 2 segundo
  
  console.log('Encerrando navegador...');
  await browser.close();
  //console.log(posts);
}

ocr();