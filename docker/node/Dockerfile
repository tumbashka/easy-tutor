FROM node:18-alpine

WORKDIR /var/www

COPY ./package.json ./package-lock.json ./

RUN npm install

COPY . .

RUN chown -R node:node /var/www
USER node

CMD ["npx", "vite", "--host", "0.0.0.0"]
