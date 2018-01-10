# LensDB

Keep track of your Lenses you bought and sold

## Usage

build docker Image:

```
docker build . <imagename>
```

run docker image:

```
docker run -p "80:80" <imagename>
```

if you wish to keep your Database

```
docker cp <container>/var/www/inventory.sqlite <local path>
docker run -p "80:80" -v "<localpath>:/var/www/inventory.sqlite" <imagename>
```