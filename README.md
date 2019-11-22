# de.systopia.onlyoffice

Integrates OnlyOffice as a document editor into CiviCRM to make writing letters easier.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.1+
* CiviCRM v5.x

## Preparation

* Install docker: \
`sudo snap install docker`
* Add your user to the docker groups: \
`sudo groupadd docker` \
`sudo usermod -aG docker $USER`
* If needed, log out and login again.
* Create a network for the two containers: \
`docker network create --driver bridge onlyoffice`
* Initialise the document server: \
`docker run --net onlyoffice -d --name onlyoffice-document-server onlyoffice/documentserver`
* Initialise the community server: \
`docker run --net onlyoffice -d -p 80:80 -p 443:443 -p 5222:5222 --name onlyoffice-community-server -e DOCUMENT_SERVER_PORT_80_TCP_ADDR=onlyoffice-document-server onlyoffice/communityserver` ¹
* If needed, the containers can be stopped/started this way: \
`docker stop/start <name>`
* Open localhost in browser.
* Set password, e-mail address and, if needed, language and time zone, then continue.
* Go to settings -> DNS settings, activate the option and type in the domain for your server. ² ³
* Create new users via "Invite users to portal" and follow the instructions on the created invitation link.

¹ If you want to adjust the port routing, after the -p parameter the first number is the outer port, then comes the inner one. \
² You need a domain or an IP of your server/computer that runs the docker containers which both of these containers and all users can reach. At best, this is a valid domain or static global IP, you can use the local IP in your network for testing, using localhost or 127.0.0.1 does not work. \
³ If you used another port for routing, remember to set it here, too, for example: "example.org:8080".

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl de.systopia.onlyoffice@https://github.com/FIXME/de.systopia.onlyoffice/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/FIXME/de.systopia.onlyoffice.git
cv en onlyoffice
```

## Configuration

* Go to Administer -> Administration Console -> OnlyOffice Configuration.
* Type in URL, user name and password for your OnlyOffice installation.
* Save.

## Usage

#### OnlyOffice:

* Go to documents.
* Create or edit a document.
* Build your template.
* You can use any Civi tokens.

#### CiviCRM:

* Select some contacts.
* The action is called "Generate PDFs via OnlyOffice".
* Select your template.
* Click on "Generate!".
* Wait until the zip file is ready to download.

## Known Issues

* You cannot select Civi tokens inside OnlyOffice.
* The OnlyOffice server is only available in HTTP with no encryption.
* The performance is very low.
* The community server talks home if not disabled (especially via mail server settings).

## Next steps

* Implement HTTPS (via local proxy or wait for the native feature).
* Rewrite the template/conversion feature as an external service for performance reasons.
* Implement small comfort features like help/hints for settings.
* Find a way to reliable disable every connection to the OnlyOffice servers.
