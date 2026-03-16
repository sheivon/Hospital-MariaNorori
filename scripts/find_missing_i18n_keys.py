import re
import json
import glob

with open('public/assets/i18n/en.json', 'r', encoding='utf-8') as f:
    en = json.load(f)
keys = set(en.keys())

pattern = re.compile(r'data-i18n(?:-placeholder|-title|-value)?="([^"]+)"')
found = set()

for path in glob.glob('**/*.php', recursive=True):
    if path.startswith('public/assets/'):
        continue
    try:
        txt = open(path, 'r', encoding='utf-8', errors='ignore').read()
    except Exception:
        continue
    for m in pattern.finditer(txt):
        found.add(m.group(1))

missing = sorted([k for k in found if k not in keys])
with open('scripts/missing_i18n_keys.txt', 'w', encoding='utf-8') as fo:
    fo.write('Missing keys (count={}):\n'.format(len(missing)))
    for k in missing:
        fo.write(k + '\n')
print('Wrote missing keys to scripts/missing_i18n_keys.txt')
