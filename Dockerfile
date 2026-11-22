FROM node:22-alpine
ENV NODE_ENV=production DATA_DIR=/app/data PORT=3000 TZ=America/Sao_Paulo
WORKDIR /app
COPY package*.json ./
RUN npm ci --omit=dev && npm cache clean --force
COPY . .
RUN mkdir -p /app/data && chown -R node:node /app
USER node
EXPOSE 3000
HEALTHCHECK --interval=30s --timeout=5s CMD wget -qO- http://127.0.0.1:3000/healthz || exit 1
CMD ["node", "--disable-warning=ExperimentalWarning", "src/server.js"]
