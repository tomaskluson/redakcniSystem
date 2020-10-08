FROM node:12.16.3

WORKDIR /code

ENV PORT 80

COPY package.json .

RUN npm install

COPY . .

CMD [ "node", "server.js" ]