# Snowy Extension
## Difficulty: very easy

> The Starshard Bauble’s theft left behind subtly altered source files hidden among festive scripts. Players must inspect code highlighting anomalies to spot injected logic, trace tampering patterns, and reveal how the Gingerbit Gremlin sabotaged Everlight, one miscolored line at a time.

## Step 1
Download the file from the site to get the file `forensics_snowy_extension.zip`, the unzip.

This will give us a **Microsoft OOXML** or a Visual Studio Code extension package file: `code-highlight-0.0.1.vsix`.

We need to extract this with the the following command:

```
unzip extension.vsix -d extension/
```
