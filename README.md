Martabak Router for OpenWRT
===========================

Ingat ini masih alpha version, kita masih perlu berbenah

memungkinkan kita untuk login agar dapat mengakses internet dengan hotspot. 

## Instalasi

Sebelumnya buat perangkat anda menjadi statis IP dengan Luci interface

di bagian Network > DHCP and DNS

pada bagian Static Leases isi IP yang anda khendaki dan mac address device anda, dengan tujuan Device tersebut adalah administrator tetap

![](https://raw.github.com/pedox/martabak-hotspot/master/img_doc/static-lease.png)


-------------------------------------------------------

Pertama anda harus menginstall beberapa paket seperti

1. lighttpd lighttpd-mod-cgi **lighttpd-mod-alias lighttpd-mod-rewrite**
2. php5 php5-cgi **php5-mod-hash php5-mod-json php5-mod-mcrypt**

Untuk instruksi selanjutnya silahkan ikuti berikut (Jika yang belum install Webserver lighttpd + php)

[http://wiki.openwrt.org/doc/howto/http.lighttpd#configuring.lighttpd.and.php5](http://wiki.openwrt.org/doc/howto/http.lighttpd#configuring.lighttpd.and.php5)

**Untuk yang sudah menginstall Lighttpd silahkan ikuti langkah berikut**

Buka file `/etc/lighttpd/lighttpd.conf` di router anda bisa menggunakan vi atau editor lain nya (saya pake FTP karena sudah install pure-ftp)

lalu buka beberapa mod dengan menghilangkan komentar ( tanda #) sehingga menjadi seperti berikut

	server.modules = ( 
		"mod_rewrite", 
	#	"mod_redirect", 
		"mod_alias", 
	#	"mod_auth", 
	#	"mod_status", 
	#	"mod_setenv",
	#	"mod_fastcgi",
	#	"mod_proxy",
	#	"mod_simple_vhost",
		"mod_cgi"
	#	"mod_ssi",
	#	"mod_usertrack",
	#	"mod_expire",
	#	"mod_webdav"
	)

setelah itu sisipkan kode berikut paling bawah pada file `lighttpd.conf`

	$HTTP["host"] != "192.168.1.1" {
		$SERVER["socket"] == ":2082" {
		    url.rewrite-once = ("^/(.*)" => "/hotspot_login/?redir=1")
	    }
	}

### Keterangan

1. **192.168.1.1** : Ganti dengan IP Router anda jika berbeda
2. **:2082** : untuk keamanan ganti Port sesuai keinginan anda (asal tidak menganggu services yang lain)
3. **/hotspot_login/** : ini opsional jika folder login anda berbeda tempat, ganti opsi ini

setelah itu restart lighttpd

	# /etc/init.d/lighttpd reload

kembali pada Luci Interface menuju ke Custom Firewall dengan mengklik Network > Firewall > Custom Rules

![](https://raw.github.com/pedox/martabak-hotspot/master/img_doc/custom-rule.png)

lalu tambahkan kode berikut. 

	iptables -t nat -A PREROUTING -s 192.168.1.0/255.255.255.0 -p tcp --dport 1:1000 -j DNAT --to-destination 192.168.1.1:2082
	iptables -t nat -I PREROUTING -s 192.168.1.2 -p tcp --dport 1:1000 -j ACCEPT

### Keterangan
	
1. **192.168.1.1** : Ganti dengan IP Router anda jika berbeda
2. **:2082** : Samakan Port tersebut dengan lighttpd tadi
3. **192.168.1.2** : Perangkat Device anda yang berhak menjadi administrator (ganti jika berbeda)

**Tambahan** jika anda ingin membuka beberapa port Contoh
	
	iptables -t nat -I PREROUTING -s 192.168.1.0/255.255.255.0 -p tcp --dport 21 -j ACCEPT
	iptables -t nat -I PREROUTING -s 192.168.1.0/255.255.255.0 -p tcp --dport 22 -j ACCEPT

Yang memungkinkan untuk mengakses FTP / SSH

Klik Submit lalu restart firewall

untuk mengecek apakah berhasil atau tidak silahkan gunakan perintah berikut

	# iptables -t nat -L PREROUTING

maka hasilnya akan menjadi 

	Chain PREROUTING (policy ACCEPT)
	target     prot opt source               destination         
	ACCEPT     tcp  --  192.168.1.0/24         anywhere            tcp dpt:ssh 
	ACCEPT     tcp  --  192.168.1.0/24         anywhere            tcp dpt:ftp  
	DNAT       tcp  --  192.168.1.0/24         anywhere            tcp dpts:1:1000 to:192.168.1.1:2082 

### Copy Halaman login

Copy halaman `hotspot_login` login ke root webserver anda

dan edit beberapa pengaturan di file `core.php`

	private $name = "Martabak Hotspot"; 			//name your hotspot
	private $router_ip = "192.168.1.1"; 			//My Router IP (also make a hotspot login)
	private $redirect_port = "2082";				//Redirect Port Default is 80
	private $login_url = "/hotspot_login/";			//Locate your Hotspot URL

yah pasti sudah ngerti lah kayak gimana :D

### Crontab

Untuk mengecek Host yang sudah tidak memakai hotspot masukan perintah `crontab -e` lalu tambah baris berikut

	* * * * * php-cgi hotspot_login/cron.php

Cron task nya bisa diubah sesuai keiningan kok jika keberatan.

### Selesai 

Hotspot sudah diapplikasikan.

![](https://raw.github.com/pedox/martabak-hotspot/master/img_doc/hotspotlogin.png)

![](https://raw.github.com/pedox/martabak-hotspot/master/img_doc/hotspotlogin-2.png)

# Perhatian

Karena ini merupakan versi alpha, beberapa error dan bug masih ditemukan jadi DO YOU OWN RISK

Tested on TP-Link TL-MR3020 v1 Frimware Version OpenWrt Barrier Breaker r35080 / LuCI Trunk (trunk+svn9605) 3.6.11



# What the next ?

1. penambahan MySQL database untuk user agar dapat login
2. control panel untuk manajemen hotspot
3. dan masih banyak lagi

### kontribusi ?

silahkan aja :D

Thanks to [OpenWRT Indonesia group](http://www.facebook.com/groups/openwrt/)

Twitter: [@engga_enak](http://twitter.com/engga_enak)