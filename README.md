# Diawi Uploader

Quick hack to upload your mobile builds to [Diawi](http://diawi.com). A tool for developers to deploy development and in-house applications directly to the devices.

[![Latest Stable Version](https://poser.pugx.org/bushbaby/diawi-uploader/v/stable)](https://packagist.org/packages/bushbaby/diawi-uploader)
[![Total Downloads](https://poser.pugx.org/bushbaby/diawi-uploader/downloads)](https://packagist.org/packages/bushbaby/diawi-uploader)
[![Latest Unstable Version](https://poser.pugx.org/bushbaby/diawi-uploader/v/unstable)](https://packagist.org/packages/bushbaby/diawi-uploader)
[![License](https://poser.pugx.org/bushbaby/diawi-uploader/license)](https://packagist.org/packages/bushbaby/diawi-uploader)
[![Monthly Downloads](https://poser.pugx.org/bushbaby/diawi-uploader/d/monthly)](https://packagist.org/packages/bushbaby/diawi-uploader)
[![Daily Downloads](https://poser.pugx.org/bushbaby/diawi-uploader/d/daily)](https://packagist.org/packages/bushbaby/diawi-uploader)

## Installation

```
composer g require bushbaby/diawi-uploader
```

Installs this globally (usually to ~/.composer) so as long as ~/.composer/bin is on your PATH you'll be able to run it's commands.

## Configuration

You'll need to add a API token from diawi.com to config/autoload/diawi.local.php


## Usage Commands

To upload a file to diawi use this simple command.

```
diawi-uploader upload <path>
```

To poll the processing of a job run the following command. Note that running the upload command will run this command automaticly.

```
diawi-uploader status <job>
```

Once the job has been successfully processed a browser is opened to the page on diawi.com

## Limitations

Currently only opens a final URL such as `https://install.diawi.com/GtYhbDr` on OSX.

Some flies we're killed during development, though not intentionally!