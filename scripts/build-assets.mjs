import { mkdir, copyFile, readFile, writeFile } from 'node:fs/promises';
import { createHash } from 'node:crypto';

const root = new URL('../', import.meta.url);
const out = new URL('public/build/', root);
await mkdir(out, { recursive: true });
const pairs = [
  ['node_modules/bootstrap/dist/css/bootstrap.min.css', 'bootstrap.min.css'],
  ['node_modules/bootstrap/dist/css/bootstrap.min.css.map', 'bootstrap.min.css.map'],
  ['node_modules/bootstrap/LICENSE', 'BOOTSTRAP-LICENSE.txt'],
  ...['tokens', 'fonts', 'corporate', 'bootstrap-adapter'].map(name => [`resources/css/${name}.css`, `${name}.css`]),
  ['resources/js/documentation.js', 'documentation.js'],
];
const hashes = {};
for (const [source, target] of pairs) {
  await copyFile(new URL(source, root), new URL(target, out));
  hashes[target] = createHash('sha256').update(await readFile(new URL(target, out))).digest('hex');
}
const bootstrap = JSON.parse(await readFile(new URL('node_modules/bootstrap/package.json', root), 'utf8'));
if (!bootstrap.version.startsWith('5.3.')) throw new Error('Se requiere Bootstrap 5.3');
await writeFile(new URL('manifest.json', out), JSON.stringify({ bootstrap: bootstrap.version, files: hashes }, null, 2));
console.log(`Bootstrap ${bootstrap.version}: ${pairs.length} recursos locales preparados; sin AdminKit.`);
