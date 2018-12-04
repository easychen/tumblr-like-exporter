#Tumblr like 图片备份工具

！[]（Https://ws1.sinaimg.cn/large/40dfde6fly1fxulhoywxpj20un0mm0xe.jpg）

##预先要求

 - 命令行支持 php （我使用7，5.4+可能运行）
 - 仔细检查您的终端环境可以直接访问tumblr

##用法

 - 打开终端
 - git clone 
```
git clone https://github.com/easychen/tumblr-like-exporter.git
```
 - 创建cookie.txt，将tumblr cookie保存到它（见下文）
 - 运行`php robo.phar export`来解析所有图像
 - 运行`php robo.phar download` 将它们下载到 Repo 下的`photos`文件夹
 - 你可以使用其他工具下载，运行`php robo.phar save2txt`生成一个用于导入的txt图像列表
 - 如果你喜欢它，可以 star 一下 😝

它也可以下载博客中的照片
 - 创建next.txt，将博客网址放在其中，如`https://www.tumblr.com/blog/modomoduo`
 - 运行`php robo.phar export`，它会询问是否要下载此页面，只需按`enter` ..


##如何找到cookie

 - 打开Chrome，登录tumblr，通常你会在`https：// www.tumblr.com / dashboard`
 - 单击右上角的菜单，“更多工具”，“开发人员工具”
！[]（Https://ws1.sinaimg.cn/large/40dfde6fly1fxujmqc1b1j20kw0i8tb7.jpg）

 - 切换到`Network`，`dashbord`，复制行中`cookie：`之后的所有文本，如果你看不到`dashboard`请刷新浏览器
！[]（https://ws1.sinaimg.cn/large/40dfde6fly1fxujh1iezkj20vi0pdae3.jpg）