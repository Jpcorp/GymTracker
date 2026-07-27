// Minimal chromium-cli-alike REPL, for hosts without chromium-cli/Playwright installed.
// Reads newline-separated commands from stdin, drives one Chromium instance.
// See SKILL.md for the docker invocation (mounts, --add-host, env).
import { chromium } from 'playwright';
import readline from 'node:readline';

const BASE_URL = process.env.BASE_URL || 'http://host.docker.internal';
const OUT_DIR = process.env.OUT_DIR || '/out';

const browser = await chromium.launch();
let context = await browser.newContext();
let page = await context.newPage();
const consoleErrors = [];

function attachListeners(p) {
  p.on('pageerror', (e) => consoleErrors.push('pageerror: ' + e.message));
  p.on('console', (msg) => {
    if (msg.type() === 'error') consoleErrors.push('console: ' + msg.text());
  });
}
attachListeners(page);

function resolveUrl(u) {
  return u.startsWith('http') ? u : BASE_URL + (u.startsWith('/') ? u : '/' + u);
}

async function handle(line) {
  const trimmed = line.trim();
  if (!trimmed || trimmed.startsWith('#')) return;
  const [cmd, ...rest] = trimmed.split(' ');
  const arg = rest.join(' ');

  try {
    switch (cmd) {
      case 'nav': {
        await page.goto(resolveUrl(arg), { waitUntil: 'networkidle' });
        console.log('OK nav ->', page.url());
        break;
      }
      case 'wait-for': {
        if (arg.startsWith('text=')) {
          await page.getByText(arg.slice(5), { exact: false }).first().waitFor({ timeout: 15000 });
        } else {
          await page.locator(arg).first().waitFor({ timeout: 15000 });
        }
        console.log('OK wait-for', arg);
        break;
      }
      case 'click': {
        await page.locator(arg).first().click();
        console.log('OK click', arg);
        break;
      }
      case 'fill': {
        const sp = arg.indexOf(' ');
        const selector = sp === -1 ? arg : arg.slice(0, sp);
        const value = sp === -1 ? '' : arg.slice(sp + 1);
        await page.locator(selector).first().fill(value);
        console.log('OK fill', selector);
        break;
      }
      case 'select': {
        const sp = arg.indexOf(' ');
        const selector = arg.slice(0, sp);
        const value = arg.slice(sp + 1);
        await page.locator(selector).first().selectOption(value);
        console.log('OK select', selector, value);
        break;
      }
      case 'press': {
        await page.keyboard.press(arg);
        console.log('OK press', arg);
        break;
      }
      case 'screenshot': {
        const name = arg || `shot-${Date.now()}`;
        const path = `${OUT_DIR}/${name.endsWith('.png') ? name : name + '.png'}`;
        await page.screenshot({ path, fullPage: true });
        console.log('OK screenshot ->', path);
        break;
      }
      case 'new-context': {
        context = await browser.newContext();
        page = await context.newPage();
        attachListeners(page);
        console.log('OK new-context (fresh, unauthenticated session)');
        break;
      }
      case 'eval': {
        const result = await page.evaluate(arg);
        console.log('OK eval ->', JSON.stringify(result));
        break;
      }
      case 'console': {
        console.log(consoleErrors.length ? consoleErrors.join('\n') : '(no console errors captured)');
        break;
      }
      case 'help': {
        console.log('nav <url> | wait-for <selector>|text=<t> | click <selector> | fill <selector> <value> | select <selector> <value> | press <key> | screenshot <name> | new-context | eval <js> | console | quit');
        break;
      }
      case 'quit':
      case 'exit': {
        await browser.close();
        process.exit(0);
      }
      default:
        console.log('ERR unknown command:', cmd, '(try: help)');
    }
  } catch (e) {
    console.log('ERR', cmd, '->', e.message.split('\n')[0]);
  }
}

const rl = readline.createInterface({ input: process.stdin });
for await (const line of rl) {
  await handle(line);
}
await browser.close();
