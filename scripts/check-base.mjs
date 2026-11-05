import { spawn } from 'node:child_process';
import process from 'node:process';
import { setTimeout as delay } from 'node:timers/promises';

const env = { ...process.env, DOCS_BASE: '/documentacion', DOCS_BASE_CHECK: '1' };
const php = process.env.PHP_BINARY ?? 'php';
const server = spawn(php, ['-S', '127.0.0.1:8081', '-t', 'public', 'scripts/router.php'], { env, stdio: 'inherit' });
server.on('error', error => { process.stderr.write(`${error.message}\n`); });
try {
  let ready = false;
  for (let i = 0; i < 120; i++) {
    try { ready = (await fetch('http://127.0.0.1:8081/documentacion/')).ok; } catch { }
    if (ready) break;
    if (server.exitCode !== null) throw new Error('El servidor del prefijo terminó al arrancar.');
    await delay(250);
  }
  if (!ready) throw new Error('El servidor del prefijo no respondió.');
  await new Promise((resolve, reject) => {
    const tests = spawn(process.execPath, ['node_modules/@playwright/test/cli.js', 'test'], { env, stdio: 'inherit' });
    tests.on('error', reject);
    tests.on('exit', code => code === 0 ? resolve() : reject(new Error(`Playwright prefijo: ${code}`)));
  });
} finally {
  server.kill();
}
