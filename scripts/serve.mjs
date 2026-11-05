import { spawn } from 'node:child_process';
import process from 'node:process';
const port = process.env.DOCS_PORT ?? '8080';
if (!/^\d{4,5}$/.test(port)) throw new Error('Puerto inválido');
const child = spawn(process.env.PHP_BINARY ?? 'php', ['-S', `127.0.0.1:${port}`, '-t', 'public', 'scripts/router.php'], { stdio: 'inherit', env: process.env });
for (const signal of ['SIGINT', 'SIGTERM']) process.on(signal, () => child.kill());
child.on('error', error => { process.stderr.write(`${error.message}\n`); process.exitCode = 1; });
child.on('exit', code => { process.exitCode = code ?? 0; });
