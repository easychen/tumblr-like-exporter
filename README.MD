# Tumblr favorate exporter

![](https://ws1.sinaimg.cn/large/40dfde6fly1fxulhoywxpj20un0mm0xe.jpg)

## Prerequire
- php( 5.4+ maybe , i use 7 ) cli required
- Double check your terminal env  whether can visit tumblr directly , esp in China 

## Usage 

- Enter terminal
- Git clone this repo
```
git clone https://github.com/easychen/tumblr-like-exporter.git
```
- Create cookie.txt , save your tumblr cookie to it ( see below  )
- Run `php robo.phar export` to parse all the images
- Run `php robo.phar download` to download them to `photos` folder under this repo ehen parsing finished 
- You can download useing other tools , run `php robo.phar save2txt` generate a txt image list to import
- Star the repo if you like it 😝  

It can download photos in blog 
- Create next.txt , put the blog url in it  just like `https://www.tumblr.com/blog/modomoduo`
- Run `php robo.phar export` , it will ask whether to download this page , just press `enter` ..  


## How to find cookie 

- Open Chrome , sign in tumblr , normal you will on `https://www.tumblr.com/dashboard`
- Click menu on the right top , `more tools` , `developer tools` 
![](https://ws1.sinaimg.cn/large/40dfde6fly1fxujmqc1b1j20kw0i8tb7.jpg)

- Switch to `Network` , `dashbord` , copy all the text after `cookie:` in the line, refresh if you can't see `dashboard`
![](https://ws1.sinaimg.cn/large/40dfde6fly1fxujh1iezkj20vi0pdae3.jpg)

