
## Better to use all in one docker or setup on server

Config is located in /docker-all-in-one

docker build -t blocksoft/factom-all ./

docker-compose stop && docker-compose down && docker-compose up –d 

docker logs factom-all

docker exec -i -t factom-all /bin/bash

docker exec -i -t factom-all /go/bin/factom-walletd


### Node

https://github.com/FactomProject/factomd

git clone https://github.com/FactomProject/factomd.git

cd factomd 

docker build -t blocksoft/factomd .

docker-compose stop && docker-compose down && docker-compose up –d 

docker exec -i -t factomd /bin/bash

### Wallet

https://github.com/FactomProject/factom-walletd

git clone https://github.com/FactomProject/factom-walletd.git

cd factom-walletd 

docker build -t blocksoft/factom-walletd .

docker-compose stop && docker-compose down && docker-compose up –d 

docker exec -i -t walletd /bin/bash

### Cli

https://github.com/FactomProject/factom-cli

## Any other help

https://github.com/FactomProject/FactomDocs/blob/master/installFromSourceDirections.md
