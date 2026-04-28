FROM node:25-alpine3.22

RUN npm install -g @ionic/cli

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

EXPOSE 8100

CMD ["ionic", "serve", "--host=0.0.0.0", "--no-open"]
