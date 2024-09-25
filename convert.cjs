const puppeteer = require('puppeteer');
const path = require('path');

async function convertImageToText(imagePath) {
    console.log('Iniciando o Puppeteer...');
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();
    console.log('Navegador iniciado, acessando o site...');
    await page.goto('https://www.onlineocr.net/pt/');

    // Carregar a imagem
    console.log('Carregando a imagem...');
    await page.waitForTimeout(2000); // Atraso de 2 segundo
    const [fileChooser] = await Promise.all([
        page.waitForFileChooser({ timeout: 60000 }), // Aumenta o timeout para 60 segundos
        page.click('#fileupload')
    ]);
    await fileChooser.accept([imagePath]);
    console.log('Imagem carregada.');

    // Acionar o botão de conversão
    console.log('Acionando o botão de conversão...');
    await page.click('#MainContent_btnOCRConvert');

    // Esperar o resultado e extrair o texto
    try {
        console.log('Esperando o resultado...');
        await page.waitForSelector('#MainContent_txtOCRResultText', { timeout: 20000 });
        const result = await page.$eval('#MainContent_txtOCRResultText', el => el.value);
        console.log('Texto extraído:', result);
        await browser.close();
        return result;
    } catch (error) {
        console.error('Erro ao extrair o texto:', error);
        await browser.close();
        return null;
    }
}

const imagePath = process.argv[2];
console.log('Caminho da imagem:', imagePath);
convertImageToText(imagePath).then(console.log).catch(console.error);