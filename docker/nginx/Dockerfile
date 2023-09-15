FROM nginx:stable

COPY ./docker/nginx/nginx.conf  /etc/nginx/
COPY ./docker/nginx/templates /etc/nginx/templates/
RUN echo "upstream php-upstream { server symapi_php:9001; }" > /etc/nginx/conf.d/upstream.conf

EXPOSE 80
EXPOSE 443
