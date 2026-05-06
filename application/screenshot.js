const puppeteer = require('puppeteer');
const fs = require('fs');
const key = "!9Mukrn=Yed,US(F4:{o)nVGK4dGg*";

//delai maximum pour procéder au screenshot en millisecondes
const maxTimeout = 118000;


/**
* arg2 identifiantEcriture
* arg3 width
* arg4 height
* arg5 domain
**/
let identifiantCreation = process.argv[2];
let identifiantEcriture = process.argv[3];
let width = parseFloat(process.argv[4]);
let height = parseFloat(process.argv[5]);

let url = "local.monjardin-materrasse.com/application/displayScreenshot";
if(process.argv.length >= 7) {
	url = process.argv[6] + "/application/displayScreenshot";
}

let scheme = "https";
if(process.argv.length >= 8) {
	scheme = process.argv[7];
}

url = scheme + "://" + url;

(async () => {
	await fs.promises.mkdir('tmp', { recursive: true });
	const browser = await puppeteer.launch({headless: true});
	const page = await browser.newPage();

	await page.setViewport({width: width, height: height});

	/**
	 * charge la page, attend l'évènement js entitesLoadingComplete
	 * procède au screenshot lorsque celui ci est fired, ou lorsque l'on a dépassé le délai maximum maxTimeout
	 * on rajouter un petit délai après le chargement des images pour permettre au script d'exécuter les fonctions de replacement, transformation, ...
	 */
	await new Promise(async resolve => {
		let entitesLoadedTimeout = setTimeout(() => {
			clearTimeout(entitesLoadedTimeout);
			resolve(true);
		}, maxTimeout);

		await page.evaluateOnNewDocument(() => {
			window.addEventListener('entitesLoadingComplete', (eventDetail) => {
				window.onCustomEvent(eventDetail);
			});
		});
		await page.goto(url + "/" + identifiantCreation + "/" +  identifiantEcriture + "/" + key);
		await page.exposeFunction('onCustomEvent', (e) => {
			clearTimeout(entitesLoadedTimeout);
			resolve(true);
		});
	}, async reject => {
		resolve(false);
	});
	await page.waitFor(2000);
	await page.screenshot({path: 'tmp/' + identifiantCreation + "_" +  identifiantEcriture + '.jpg',omitBackground:true});
	await browser.close();

	console.log("OK");
})();
