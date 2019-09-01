export default
`<h3 style="margin: 8px">域名</h3><b>纯字符串</b>：当此字符串匹配目标域名中任意部分，该规则生效。比如 sina.com 可以匹配 sina.com 、 sina.com.cn 和 www.sina.com ，但不匹配 sina.cn。
<b>正则表达式</b>：由<code>regexp:</code>开始，余下部分是一个正则表达式。当此正则表达式匹配目标域名时，该规则生效。例如<code>regexp:\\\\.goo.*\\\\.com$</code>匹配 www.google.com 、 fonts.googleapis.com ，但不匹配 google.com。
<b>子域名 (推荐)</b>：由<code>domain:</code>开始，余下部分是一个域名。当此域名是目标域名或其子域名时，该规则生效。例如<code>domain:v2ray.com</code>匹配 www.v2ray.com 、 v2ray.com ，但不匹配 xv2ray.com。
<b>完整匹配</b>：由<code>full:</code>开始，余下部分是一个域名。当此域名完整匹配目标域名时，该规则生效。例如<code>full:v2ray.com</code>匹配 v2ray.com 但不匹配 www.v2ray.com。
<b>预定义域名列表</b>：由<code>geosite:</code>开头，余下部分是一个名称，如<code>geosite:google</code>或者<code>geosite:cn</code>。名称及域名列表参考<a href="https://v2ray.com/chapter_02/03_routing.html#dlc" target="_blank">预定义域名列表</a>。
<b>从文件中加载域名</b>：例如<code>ext:file:tag</code>，必须以<code>ext:</code>（小写）开头，后面跟文件名和标签，文件存放在资源目录中，文件格式与<code>geosite.dat</code>相同，标签必须在文件中存在。
<h3 style="margin: 8px">IP</h3><b>IP</b>：例如<code>127.0.0.1</code>
<b>CIDR</b>：例如<code>192.168.1.0/24</code>
<b>GeoIP</b>：例如<code>geoip:cn</code>，必须以<code>geoip:</code>（小写）开头，后面跟双字符国家代码，支持几乎所有可以上网的国家。特殊值：<code>geoip:private</code>(V2Ray 3.5+)，包含所有私有地址，如<code>127.0.0.1</code>。
<b>从文件中加载 IP</b>：例如<code>iext:file:tag</code>，必须以<code>iext:</code>（小写）开头，后面跟文件名和标签，文件存放在资源目录中，文件格式与<code>geoip.dat</code>相同标签必须在文件中存在
<p style="color: #F56C6C">V2rayV 从文件加载中加载 IP 的指令和 V2ray 略有区别，以<code>iext:</code>（小写）开头，请知晓。</p>
<h3>V2rayV 的资源目录： [V2rayV 目录]/storage/app/v2ray</h3>`;
