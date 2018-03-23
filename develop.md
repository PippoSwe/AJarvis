# Manuale sviluppatore

Per consentire lo sviluppo dell'applicazione ai diversi sotto-team del gruppo è necessario disporre di un'ambiente di sviluppo che eviti la scrittura contemporanea dei sorgenti del prodotto.

L'editing concorrente dei sorgenti utilizzando i tradizionali protocolli per l'accesso remoto quali FTP o SSH introduce problemi di concorrenza e accesso ai singoli file.

L'installazione di un'ambiente che fornisce tutti gli strumetni per lo sviluppo dell'applicativo è un' operazione che normalmente:

- **richiede molto tempo**: perchè installare lo stack (raccolta di software per realizzare siti e applicazioni web)  costringe il sistemista a effettuare molte operazioni per preparare l'ambiente di sviluppo
- **itroduce problemi di compatibilità**: poichè l'installazione dello stack su diversi sistemi operativi comporta generalmente la modifica delle configurazioni degli elementi che compongono la pila

## Docker

Riferimenti: https://it.wikipedia.org/wiki/Docker

Docker è un progetto open-source che automatizza il deployment (consegna o rilascio al cliente, con relativa installazione e messa in funzione o esercizio, di una applicazione o di un sistema software tipicamente all'interno di un sistema informatico aziendale) di applicazioni all'interno di container software.

L'utilizzo di Docker per creare e gestire i container può semplificare la creazione di sistemi distribuiti, permettendo a diverse applicazioni o processi di lavorare in modo autonomo sulla stessa macchina fisica o su diverse macchine virtuali. Ciò consente di effettuare il deployment di nuovi nodi solo quando necessario, permettendo uno stile di sviluppo del tipo platform as a service (PaaS).

L'utilizzo di docker consente di replicare la pila software in tempi relativamente brevi dato che i container sono già presednti in formato in maggine nell' HUB/Repository delle immagini messe a disposizione per e dalla comunità open-source.

Ad esempio per costruire la nota pila Apache/PHP/MySQL Docker fornisce le immagini pronte all'uso:

- Apache\PHP
- MySQL 

L'utilizzo dei Dockerfile e dei docker-compose.yml consente di costruire un sistema basato su microservizi in tempi rapidi (download delle immagini ed eventuale installazione di pacchetti mancanti)

## Installazione

L'installazione dell'ambiente per lo sviluppo del prodotto Ajarvis viene effettuata utilizzando le teconologie sopracitate.

### Prerequisiti

Di seguito riportiamo il software necessario al processo di sviluppo del prodotto Ajarvis:

- Docker: https://www.docker.com/community-edition#/download
- docker-compose: https://docs.docker.com/compose/install/

### Preparazione dell'ambiente di lavoro

Per sviluppare il prodotto è sufficiente ottenere i file che consentono l'installazione e la configurazione del sistema.

```bash
$ git clone https://github.com/PippoSwe/AJarvis.git ajarvis-dev
$ cd ajarvis-dev
$ docker-compose up
```

Una volta eseguiti questi comandi verranno:

- scaricate le immagini delle macchine necessarie al funzionamento di ajarvis (Apache, MySQL)
- aggiunti i pacchetti mancanti ai diversi sistemi operativi (es: ffmpeg) 
- create le cartelle per iniziare lo sviluppo dell'applicativo
  - **dbms**: configurazioni MySQL

### Installazione Ajarvis

E' ora possibile scraricare il sorgente ajarvis per effettuare l'installazione.

```bash
$ docker exec -i ajarvis-rest composer update
$ docker exec -i ajarvis-rest curl "http://127.0.0.1/migrate/" > /dev/null
```

Dopo aver installato il sorgente ajarvis è necessario installare le librerie PHP necessarie al funzionamento  dell'applicativo.

L'installazione può essere lanciata dal container Docker utilizzato per la pubblicazione HTTP/HTTPS.

```bash
$ docker exec -i -t ajarvis-rest /bin/bash
$ composer update
```

### Test funzionamento ambiente

I test riportati presuppongono l'installazione standard dell'ambiente come descritto sopra. E' tuttavia possibile riconfigurare l'ambiente di sviluppo per modificare l'ambiente stesso in base alle proprie esigenze:

- porte pubblicazione HTTP/HTTPs
- porte MYSQL
- ....

#### Apache/PHP

Il container Apache/PHP effettua la pubblicazione dei contenuti via HTTP sulle porte:

- 8080: http
- 8443: https

Per verificare l'avvenuta installazione dell'ambiente di sviluppo collegarsi alla pagina: https://localhost:8443/

#### MySQL

Il container MySQL è l'archivio metadati dell'applicativo ajarvis. Il "demone" MySQL è accessibile da tutte le interfaccie di rete sulla porta 3307.

Tuttavia i container Docker comunicano tra loro utilizzando la rete bridge appositamante per consetire loro di comunicare in modo sicuro.

Per questo motivo la porta 3306 viene utilizzata internamente per la comunicazione dai container verso il container MySQL **dbms**.

E' possibile utilizzare un qualsiasi client MySQL per accedere alla banca dati ajarvis utilizzando le credenziali: 

- **Utente**: ajarvis
- **Password**: ajarvis


## Ambiente di sviluppo

Per lo sviluppo del prodotto può essere utile disporre di un'ambiente integrato per effettuare comuni operazioni durante il ciclo di sviluppo.

Di seguito riportiamo la tipologia di operazione la sua utilità e la frequenza di tale operazione per consentire al lettore di decidere se utilizzare l'ambiente integrato.

|       Tipo operazione       | Tipo sviluppatore |                           Utilità                            | Frequenza |
| :-------------------------: | :---------------: | :----------------------------------------------------------: | :-------: |
|      **Avvio docker**       |     GUI, REST     | Permette di avviare le macchine Docker (REST, MySQL). Tale operazione è sostituibile dal comando **docker-compose up** |   Bassa   |
| **Riconfigurazione Docker** |     GUI, REST     | Permette di ricompilare le macchine docker. L'operazione viene effettuata quando vengono aggiunti nuovi pacchetti all' SO (es: ffmpeg) |   Bassa   |
|         **Testing**         |       REST        | Consente di eseguire i test di unità per verificare il Code Coverage e t test di regressione automatici |   Alta    |

### PHPStorm

L'ambiente integrato scelto per effettuare lo sviluppo del prodotto è **PHPStorm** versione **2017.3.4**.

Dopo aver installato AJarvis è possibile configurare la cartella **ajarvis-dev** come progetto PHPStorm utilizzado la funzione *Apri cartella ….*

Di seguito riportiamo i passi di dialogo in PHPStorm per effettuare la configurazione per le diverse tipologie di operazioni.

#### Docker

Se il file docker-compose.yml è presente nella root directory del progetto PHPStorm permetterà di configurare le funzioni Docker in modo semi-automatico.

Per configurare tale servizio utilizzare il menu in alto a dx: 

Edit Configurations > (+ Aggiungi) > Docker-compose 

- **Name**: Docker hosts
- **Compose file**: selezionare dalla tendina docker-compose.yml

#### PHP Client

E' necessario configurare l'interprete PHP client per:

- Eseguire i test di unità
- Lanciare script da linea di comando

Per configurare tale servizio utilizzare il menu PHPStorm > Preferences: 

Languages & Framework > PHP > Cli interpreter > (…) > (+ Aggiungi)  > From Docker, Vagrant, VM Remote … 

- **Docker** (radio button)
- **Image name**: ajarvisdev_web:latest
- **Name**: Ajarvis REST
- **Docker Container**: -v /Users/mfasolo/PhpstormProjects/ajarvis-dev:/var/www (utilizzare … per modificarlo)

#### Test unit

Per configurare tale servizio utilizzare il menu PHPStorm > Preferences: 

Languages & Framework > PHP > Test Frameworks >  (+ Aggiungi) > PHPUnit by Remote interpreter

- **Intepreter**: Ajarvis REST
- **Path to script**: /var/www/html/vendor/autoload.php
- **Default configuration file**: /var/www/html/application/tests/phpunit.xml

Dopo avere definito il client phpunit per effettuare i test è necessario aggiungere il link (Play) per eseguire il run dei tests.

Edit Configurations > (+ Aggiungi) > PHPUnit

- **Name**: Ajarvis Tests
- **Defined in configuration file** (radio button)

**N.B.**: Poichè PHPstorm utilizza il client phpunit con connessione proveniente da un'interfaccia esterna al bridge dei container, per eseguire i tests è necessario cambiare le configurazione del file web/html/application/config/database.php in accordo con le configurazioni del proprio host.

# AJarvis API

Per consentire agli sviluppatori delle interfacce di ottenere informazioni sulle chiamate REST offerte da ajarvis è stato installato il servizio swagger.

La documentazione delle api è raggiungibile a questo indirizzo http://localhost:9080/

## Documentare API Rest

Per documentare le API REST è stato utilizzato il formato swagger. 

I metodi dei controller che espongono le API sono stati commentati utilizzando [doctrine annotations](http://doctrine-common.readthedocs.org/en/latest/reference/annotations.html) e s[wagger-php](https://github.com/zircote/swagger-php) marker specifici.

Utilizzando [swagger-makdown](https://github.com/syroegkin/swagger-markdown) è possibile generare documentazione in formato markdown partire da un file YAML (Swagger 2.0).

Di seguito riportiamo i passi per effettuare la generazione della documentazione in formato markdown.

- Scaricare il file JSON da https://localhost:8443/api/swagger/
- Convertire il file in YAMLutilizzando http://editor.swagger.io/#/ (File -> Import JSON, Export as YAML)
- Utilizzare [swagger-markdown](https://github.com/syroegkin/swagger-markdown) per convertire il file YAML in Markdown

## Elenco API per la consultazione offline

Documentazione delle API HTTP/HTTPS esposte dal microservizio AJarvis

**Version:** 0.1

**Terms of service:**  <http://swagger.io/terms/>

**Contact information:**  Pippo.swe API Team  

**License:** GPL 3

### keyword/

------

##### **GET**

**Summary:** List keywords

**Description:** List all keywords

**Parameters**

| Name   | Located in | Description               | Required | Schema  |
| ------ | ---------- | ------------------------- | -------- | ------- |
| limit  | query      | Retrieve {limit} elements | No       | integer |
| offset | query      | Pagination index start    | No       | string  |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

##### **POST**

**Summary:** Create keyword

**Description:** Create new keyword

**Parameters**

| Name    | Located in | Description | Required | Schema |
| ------- | ---------- | ----------- | -------- | ------ |
| keyword | query      | Keyword     | Yes      | string |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### keyword/{keyword_id}/

------

##### **GET**

**Summary:** View keyword

**Description:** View all details for a keyword

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| keyword_id | path       | Keyword id  | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 404  | Page Not Found        |
| 500  | Internal Server Error |

##### **PUT**

**Summary:** Update keyword

**Description:** Update keyword attributes

**Parameters**

| Name       | Located in | Description  | Required | Schema  |
| ---------- | ---------- | ------------ | -------- | ------- |
| keyword_id | path       | Keyword id   | Yes      | integer |
| keyword    | query      | Project name | Yes      | string  |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

##### **DELETE**

**Summary:** Delete keyword

**Description:** Delete keyword

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| keyword_id | path       | Keyword id  | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### member/

------

##### **GET**

**Summary:** List members

**Description:** List all members

**Parameters**

| Name   | Located in | Description               | Required | Schema  |
| ------ | ---------- | ------------------------- | -------- | ------- |
| limit  | query      | Retrieve {limit} elements | No       | integer |
| offset | query      | Pagination index start    | No       | string  |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

##### **POST**

**Summary:** Create member

**Description:** Create new member

**Parameters**

| Name      | Located in | Description      | Required | Schema |
| --------- | ---------- | ---------------- | -------- | ------ |
| firstname | query      | Member firstname | Yes      | string |
| lastname  | query      | Member lastname  | Yes      | string |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### member/{member_id}/

------

##### **GET**

**Summary:** View memeber

**Description:** View all details for a member

**Parameters**

| Name      | Located in | Description | Required | Schema  |
| --------- | ---------- | ----------- | -------- | ------- |
| member_id | path       | Member id   | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 404  | Page Not Found        |
| 500  | Internal Server Error |

##### **PUT**

**Summary:** Update member

**Description:** Update member attributes

**Parameters**

| Name      | Located in | Description      | Required | Schema  |
| --------- | ---------- | ---------------- | -------- | ------- |
| member_id | path       | Member id        | Yes      | integer |
| firstname | query      | Member firstname | Yes      | string  |
| lastname  | query      | Member lastname  | Yes      | string  |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

##### **DELETE**

**Summary:** Delete member

**Description:** Delete member

**Parameters**

| Name      | Located in | Description | Required | Schema  |
| --------- | ---------- | ----------- | -------- | ------- |
| member_id | path       | Member id   | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### project/

------

##### **GET**

**Summary:** List projects

**Description:** List all projects

**Parameters**

| Name   | Located in | Description               | Required | Schema  |
| ------ | ---------- | ------------------------- | -------- | ------- |
| limit  | query      | Retrieve {limit} elements | No       | integer |
| offset | query      | Pagination index start    | No       | string  |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

##### **POST**

**Summary:** Create project

**Description:** Create new project

**Parameters**

| Name    | Located in | Description  | Required | Schema |
| ------- | ---------- | ------------ | -------- | ------ |
| project | query      | Project name | Yes      | string |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### project/{project_id}

------

##### **GET**

**Summary:** View project

**Description:** View all details for a project

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 404  | Page Not Found        |
| 500  | Internal Server Error |

##### **PUT**

**Summary:** Update project

**Description:** Update project attributes

**Parameters**

| Name       | Located in | Description  | Required | Schema  |
| ---------- | ---------- | ------------ | -------- | ------- |
| project_id | path       | Project id   | Yes      | integer |
| project    | query      | Project name | Yes      | string  |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

##### **DELETE**

**Summary:** Delete project

**Description:** Delete project

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### project/{project_id}/keyword/

------

##### **GET**

**Summary:** List keywords for a project

**Description:** List all keywords associated to this projects

**Parameters**

| Name       | Located in | Description               | Required | Schema  |
| ---------- | ---------- | ------------------------- | -------- | ------- |
| project_id | path       | Project id                | Yes      | integer |
| limit      | query      | Retrieve {limit} elements | No       | integer |
| offset     | query      | Pagination index start    | No       | string  |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

##### **POST**

**Summary:** Associate keyword to project

**Description:** Associate keyword to this project

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |
| keyword_id | query      | Keyword id  | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### project/{project_id}/keyword/{keyword_id}/

------

##### **GET**

**Summary:** View project and keyword

**Description:** View project and keyword attributes

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |
| keyword_id | path       | Keyword id  | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 404  | Page Not Found        |
| 500  | Internal Server Error |

##### **DELETE**

**Summary:** Delete project

**Description:** Delete project

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |
| keyword_id | path       | Keyword id  | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### project/{project_id}/member/

------

##### **GET**

**Summary:** List members for a project

**Description:** List all members associated to this projects

**Parameters**

| Name       | Located in | Description               | Required | Schema  |
| ---------- | ---------- | ------------------------- | -------- | ------- |
| project_id | path       | Project id                | Yes      | integer |
| limit      | query      | Retrieve {limit} elements | No       | integer |
| offset     | query      | Pagination index start    | No       | string  |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

##### **POST**

**Summary:** Associate member to project

**Description:** Associate member to this project

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |
| member_id  | query      | Member id   | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### project/{project_id}/member/{member_id}/

------

##### **GET**

**Summary:** View project and member

**Description:** View project and member attributes

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |
| member_id  | path       | Member id   | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 404  | Page Not Found        |
| 500  | Internal Server Error |

##### **DELETE**

**Summary:** Delete member

**Description:** Delete member

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |
| member_id  | path       | Member id   | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### project/{project_id}/standup/

------

##### **GET**

**Summary:** List standups for a project

**Description:** List all standups registered in this projects

**Parameters**

| Name       | Located in | Description               | Required | Schema  |
| ---------- | ---------- | ------------------------- | -------- | ------- |
| project_id | path       | Project id                | Yes      | integer |
| limit      | query      | Retrieve {limit} elements | No       | integer |
| offset     | query      | Pagination index start    | No       | string  |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 404  | Page Not Found        |
| 500  | Internal Server Error |

##### **POST**

**Summary:** Register standup

**Description:** Register a standup in a project

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

### project/{project_id}/standup/{standup_id}/

------

##### **GET**

**Summary:** View standup

**Description:** View standup attributes

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |
| standup_id | path       | Keyword id  | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

##### **PUT**

**Summary:** Register standup

**Description:** Register a standup in a project

**Parameters**

| Name       | Located in | Description         | Required | Schema  |
| ---------- | ---------- | ------------------- | -------- | ------- |
| project_id | path       | Project id          | Yes      | integer |
| standup_id | path       | Standup id          | Yes      | integer |
| standup    | query      | Standup description | Yes      | string  |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |

##### **DELETE**

**Summary:** Delete standup

**Description:** Delete standup

**Parameters**

| Name       | Located in | Description | Required | Schema  |
| ---------- | ---------- | ----------- | -------- | ------- |
| project_id | path       | Project id  | Yes      | integer |
| standup_id | path       | Keyword id  | Yes      | integer |

**Responses**

| Code | Description           |
| ---- | --------------------- |
| 200  | Success               |
| 500  | Internal Server Error |