FROM golang:1.10

# Get git
RUN apt-get update \
    && apt-get -y install curl git \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Get glide
RUN go get github.com/Masterminds/glide

# Where factomd sources will live
WORKDIR $GOPATH/src/github.com/FactomProject/factomd

# Get the dependencies
COPY glide.yaml glide.lock ./

# Install dependencies
RUN glide install -v

# Get goveralls for testing/coverage
RUN go get github.com/mattn/goveralls

# Populate the rest of the source
COPY ./factomd .

ARG GOOS=linux

# Build and install factomd
RUN go install -ldflags "-X github.com/FactomProject/factomd/engine.Build=`git rev-parse HEAD` -X github.com/FactomProject/factomd/engine.FactomdVersion=`cat VERSION`"

# WALLET!!!
# Populate the rest of the source
WORKDIR $GOPATH/src/github.com/FactomProject/factom-walletd
COPY ./factom-walletd/glide.yaml ./factom-walletd/glide.lock ./
RUN glide install -v
COPY ./factom-walletd .
RUN glide install -v
RUN go install -ldflags "-X github.com/FactomProject/factom-walletd/vendor/github.com/FactomProject/factom/wallet.WalletVersion=`cat ./vendor/github.com/FactomProject/factom/wallet/VERSION`"
# END WALLET!!!

#CLI!!!
WORKDIR $GOPATH/src/github.com/FactomProject/factom-cli
COPY ./factom-cli/glide.yaml ./factom-cli/glide.lock ./
RUN glide install -v
COPY ./factom-cli/ .
RUN go install
# END CLI

# Setup the cache directory
RUN mkdir -p /root/.factom/m2
COPY ./factomd/factomd.conf /root/.factom/m2/factomd.conf

#CMD /go/bin/factom-walletd && /go/bin/factomd -sim_stdin=false
COPY start.sh start.sh
CMD /bin/bash start.sh

EXPOSE 8088 8089 8090 8108 8109 8110