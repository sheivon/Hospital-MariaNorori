

<!DOCTYPE HTML>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript"></script>

<script type="text/javascript">
if (typeof(jQuery) == 'undefined')  
        document.write(unescape("%3Cscript src='/wwSC.axd?r=Westwind.Web.Resources.jquery.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<title>
	Client Templating with jQuery - Rick Strahl's Web Log
</title><meta http-equiv="x-ua-compatible" content="ie=edge" /><meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1" /><link id="ctl00_RssLink" rel="alternate" type="application/rss+xml" title="Rick Strahl&#39;s Web Log" href="https://feeds.feedburner.com/rickstrahl" /><meta id="ctl00_metaKeyWords" name="Keywords" content="Template,JavaScript,Client" /><meta id="ctl00_metaDescription" name="Description" content="Client templating in Javascript can be a great tool to reduce the amount of code you have to write to create markup content on the client. There are a number of different ways that templating can be accomplished from a purely manual approach." /><link rel="stylesheet" href="/scripts/fontawesome/css/all.min.css?v=1.26" type="text/css" />

    <style type="text/css">
        .blogimgtag  { border:none;margin-top:5px; }
    
        a.paypal {
            background: steelblue;margin: 5px 10% 5px 10%; padding: 4px 4px 2px 4px; text-align: center; display: block;
            cursor: pointer;
            border-radius: 4px;
        }
        a.paypal:hover {
            background: rgb(46, 115, 172);
        }
        a.paypal img {
            height: 30px;
        }
        .product-icon { max-width: 1.1em; display: inline; vert-align: bottom  }
    </style>
    
   
    
        <link rel="shortcut icon" href="/favicon.png" type="image/png" />
        <link rel="apple-touch-icon" href="/favicon.png" />
        <link rel="search" type="application/opensearchdescription+xml" href="/opensearch.xml" title="Search Rick Strahl's Blog">         
        <link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://weblog.west-wind.com/rsd.xml" />
    
    <meta name="author" content="Rick Strahl" />

    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="Client Templating with jQuery" />
    <meta name="twitter:description" content="Client templating in Javascript can be a great tool to reduce the amount of code you have to write to create markup content on the client. There are a number of different ways that templating can be accomplished from a purely manual approach." />
    <meta name="twitter:creator" content="@rickstrahl" />
    <meta name="twitter:domain" content="weblog.west-wind.com" />

    <meta property="og:title" content="Client Templating with jQuery" />
    <meta property="og:description" content="Client templating in Javascript can be a great tool to reduce the amount of code you have to write to create markup content on the client. There are a number of different ways that templating can be accomplished from a purely manual approach." />    
    <meta property="og:type" content="article" />
    <meta property="article:author" content="https://facebook.com/rickstrahl" />
    <meta property="og:url" content="https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery" />

      
    
    <link href="/ShowPost.css?v=1.26" rel="stylesheet" type="text/css" />
    <style>
        #ArticleBody {
            margin-right: 0;            
        }

        *.code-badge {
            padding: 8px !important;
            background: #505050 !important;
        }
        /*#Article img {
            pointer-events: none;
        }*/
    </style>

<script src="/scripts/ww.jQuery.js" type="text/javascript"></script>

<script src="/ShowPost.js" type="text/javascript"></script>
<link href="../../../../App_Themes/Standard/Standard.css?v=1.26" type="text/css" rel="stylesheet" /></head>
<body>

<div id="page-wrapper">
<div id="toplevel">
    
<form method="post" action="./Client-Templating-with-jQuery" onsubmit="javascript:return WebForm_OnSubmit();" id="aspnetForm">
<div class="aspNetHidden">
<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="/wEPDwUJMjU0MTI3NzE4ZBgBBRVjdGwwMCRDb250ZW50JENhcHRjaGEPMpICAAEAAAD/////AQAAAAAAAAAMAgAAAE1XZXN0d2luZC5XZWIuV2ViRm9ybXMsIFZlcnNpb249Mi44NS4wLjAsIEN1bHR1cmU9bmV1dHJhbCwgUHVibGljS2V5VG9rZW49bnVsbAUBAAAAJ1dlc3R3aW5kLldlYi5Db250cm9scy5EaXNwbGF5RXhwcmVzc2lvbgcAAAANRXhwZWN0ZWRWYWx1ZQZWYWx1ZTEGVmFsdWUyCU9wZXJhdGlvbgJJZAxVbmlxdWVQYWdlSWQHRW50ZXJlZAAAAAEBAQAICAgNAgAAAAYAAAADAAAAAwAAAAYDAAAAASsGBAAAAAg2ZDcxMWQzMwYFAAAAAODYXQYuH95IC2S7fbO8mVOBCjeKkGZKDd/mlqFhA8R3g3RRNIP7c29fCQ==" />
</div>


<script type="text/javascript">
//<![CDATA[
var scriptVars = {
	webBasePath: "/"
};
var serverVars = {
	commentMaxLength: 4000,
	txtBodyId: "ctl00_Content_txtBody",
	txtAuthorId: "ctl00_Content_txtAuthor"
};
function WebForm_OnSubmit() {
var CpCtl = document.getElementById('6d711d33');
if (CpCtl) CpCtl.value += '_6d711d33';
return true;
}
//]]>
</script>

<div class="aspNetHidden">

	<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="3F8968ED" />
	<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="/wEdAAImZ0XoK62dGxCsgtyWWyddARF5kFqnCFqtyoWIwXSnS4MXXyRFblORSQns2ChA/4RyIQ8Z7CmT74eIIG37O/YU" />
</div>

<div class="pagemarquee">           
   <img class="hero-image" src="/images/HeroImages/RickHero18.jpg"  />   


   <img class="author-image"  src="/images/rick175x175.jpg"/>     
    <header class="blog-name-container" onclick="window.location='/';">
        <h2 style="margin: 0">Rick Strahl's Weblog &nbsp;</h2>
        <b>Wind, waves, code and everything in between...<br />
           .NET &bull; C# &bull; Markdown &bull; WPF &bull; All Things Web
        </b>
    </header>  
</div>
    
<div class="marquee-bottom-menu" style="margin-bottom: 0;">    
    <div class="marquee-bottom-sharing" style="float: right; margin-right:20px; margin-top: -20px; font-size: 35px;  ">        
        <a href="https://twitter.com/rickstrahl" title="Rick Strahl on Twitter" >
            <span class="fa-stack" style="line-height: 1em;width: 1em;height: 1em;">
                <i class="fas fa-square fa-stack-1x" style=" color: white; font-size: 0.95em "></i>
                <i class="fab fa-twitter-square fa-stack-1x" style=" color: rgb(53, 155, 240);"></i>                
            </span>
        </a>        
        <a href="https://feeds.feedburner.com/rickstrahl" title="RSS feed for Rick Strahl's Weblog">
         <span class="fa-stack" style="width: 1em;line-height: 1em;height: 1em;">
                <i class="fas fa-square fa-stack-1x" style=" width: 20px; color: white; font-size: 0.9em "></i>
                <i class="fas fa-rss-square fa-stack-1x" style="width: 20px; color: #ff6a00"></i>                
         </span>
        </a>
        
    </div>

    <span class="marquee-bottom-links">
        <a href="https://west-wind.com/contact/" title="">Contact</a> &nbsp; &bull; &nbsp;
        <a href="https://west-wind.com/articles.aspx">Articles</a>  <span class="hide-small">&nbsp; &bull; &nbsp;</span>
        <a href="https://store.west-wind.com" class="hide-small">Products</a>  <span class="hide-small">&nbsp; &bull; &nbsp;</span> 
        <a href="https://support.west-wind.com" class="hide-small">Support</a>  <span class="hide-small">&nbsp; &bull; &nbsp;</span> 
        <a href="https://weblog.west-wind.com/advertise" class="hide-small">Advertise</a>
    </span>             
</div>
  
    
    <div id="SponsorPanel"
     onclick="window.location.href='https://websurge.west-wind.com?utm_campaign=westwind-weblog-sponsored'"
     title="Please stop by our friends at West Wind Technologies who are graciously&#010providing sponsorship."
     style="cursor: pointer">
    
    <div class="ad-free-msg">
        Sponsored by:
    </div> 
    <div style="flex: auto;">
        <a href="https://websurge.west-wind.com?utm_campaign=westwind-weblog-sponsored"
           title="Please stop by our friends at West Wind Technologies who are graciously&#010providing sponsorship to remove all other ads on this site.">
            <img src="https://websurge.west-wind.com/images/WebSurgeLogo.png"
                 style="height: 1.3em;  vertical-align: top" />
            <b><span class="hidable-xs">West Wind </span>WebSurge</b>
        </a>
        - Rest Client and Http Load Testing for Windows
        <a href="https://websurge.west-wind.com?utm_campaign=westwind-weblog-sponsored"
           title="Please stop by the WebSurge site to Please stop by our friends at West Wind Technologies who are graciously&#010providing sponsorship to remove all other ads on this site.">
            <i class="fa fa-external-link" style="color: #333; padding: 0 5px; font-weight: bold"></i>
        </a>
    </div>    
    <div class="right">
        <a href="/advertise" ><span style="font-size: 0.7em">advertise here</span></a>
    </div>
</div>

<div class="clearfix"></div>


<div class="post-container">

    <aside class="post-sidebar" >
        <div class="author-name">Rick Strahl</div>
        <nav class="twitter-name" style="font-weight: bold;">
            <a href="https://twitter.com/rickstrahl" title="Rick Strahl on Twitter">
                @RickStrahl
            </a>
        </nav>


        
        <nav class="sidebar-group" style="margin-top: 10px;">
            <div><a  href='/posts' ><i class='far fa-file-alt'></i>  Posts - 1287</span></a></div>
<div><a href='/comments' ><i class='far fa-comments'></i> Comments - 15626</a></div>
 
            <div>
                <a href="https://feeds.feedburner.com/rickstrahl"
                    title="RSS feed for this Weblog">
                    <i class="fa fa-rss-square" style="color: orange" class="product-icon"></i>
                    RSS Feed
                </a>
            </div>
            <div>
                 

            </div>
        </nav>

        <div class="sidebar-header">Rick's Sites</div>
        <nav class="sidebar-group">
            <div>
                <img src="https://webconnection.west-wind.com/favicon.png" class="product-icon" />
                <a href="https://west-wind.com"
                    title="Rick's company home page.">West Wind Technologies</a>
            </div>
            <div>
                <a href="https://github.com/RickStrahl?tab=repositories"
                    title="Rick Strahl's Open Source and Sample projects on GitHub">
                    <i class="fab fa-github"></i>
                    Rick's GitHub Projects
                </a>
            </div>
            <div>
                <a href="https://west-wind.com/wconnect/weblog/"
                    title="Rick's other blog to discuss FoxPro and Web Connection topics">
                    <img src="https://www.west-wind.com/images/foxIcon_small.gif" class="product-icon" />
                    Rick's FoxPro Web Log
                </a>
            </div>
            <div>
                <a href="https://support.west-wind.com/"
                    title="West Wind Technologies Support forum">
                    <img src="https://support.west-wind.com/images/icon.png" class="product-icon"/>
                    West Wind Support Site
                </a>
            </div>
            <div>
                <a href="https://anti-trust.rocks" title="Rick's Old School Punk Rock Project">
                    <img src="https://anti-trust.rocks/favicon.png" class="product-icon" /> 
                    Anti-Trust: Punk Rock Music
                </a>
            </div>            
            
            <div>
                <a href="https://goldenbeartshirts.com" title="Bear Styled Custom T-Shirts">
                    <img src="https://pfy-prod-image-storage.s3.us-east-2.amazonaws.com/16851761/6b436631-ed8a-4c82-a81c-30b2128cf04a" class="product-icon" /> 
                    Golden Bear T-Shirts
                </a>
            </div>            
        </nav>
        
        <div class="sidebar-header">Rick's Products</div>
        <nav class="sidebar-group">
             <div>
                <a href="https://markdownmonster.west-wind.com"
                   title="Markdown Monster - a powerful Markdown Editor for Windows">
                    <img src="https://markdownmonster.west-wind.com/images/MarkdownMonster_Icon_32.png" class="product-icon" /> 
                    Markdown Monster
                </a>
            </div>
      
            <div>
                <a href="https://websurge.west-wind.com"
                   title="West Wind WebSurge: Http Request and Load Testing on Windows">
                    <img src="https://websurge.west-wind.com/favicon.png" class="product-icon" /> 
                    WebSurge
                </a>
            </div>

            <div>
                <a href="https://documentationmonster.com"
                   title="Documentation Monster - Create Markdown based structured documentation">
                    <img src="https://documentationmonster.com/images/DocumentationMonster_Icon_32.png" class="product-icon" /> 
                    Documentation Monster
                </a>
            </div>

           
            <div>
                <a href="https://webconnection.west-wind.com"
                   title="West Wind Web Connection: Build Visual FoxPro Web applications">
                    <img src="https://webconnection.west-wind.com/favicon.png" class="product-icon" />
                    West Wind Web Connection
                </a>
            </div>
        </nav>            




           
         <hr />
    
          <!-- Twitter List -->

    </aside>
    
    <main class="post-content" >        
         
    

<article id="Article" 
         itemscope itemtype="http://schema.org/BlogPosting" itemprop="blogPost" >   

    <header>
    <h2 itemprop="headline_name" >
        <a id="PostTitle" href='https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery' style="text-decoration:none;">Client Templating with jQuery</a>
            
    </h2>
    </header>    
    <hr />

 
    <div class="byline" >
        <div class="share-box" style="float:right; margin-top: -10px;">
            <span class="share-label hidable-xs">Share on:</span>
                                        
                                                    
            <a href="https://twitter.com/intent/tweet?url=https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery&amp;text=Client Templating with jQuery&amp;via=RickStrahl" target="_blank" title="Tweet on Twitter">
                <i class="fab fa-twitter-square" style=" color: rgb(51, 132, 217);"></i>                                         
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery&amp;t=Client Templating with jQuery" target="_blank" title="Share on Facebook">
               <i class="fab fa-facebook-square" style=" color: rgb(63, 96, 170); "></i>                                
            </a>
            <a href="http://www.reddit.com/submit?url=https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery&amp;title=Client Templating with jQuery" target="_blank" title="Submit to Reddit">
                <i class="fab fa-reddit-square" style=" color: rgb(245, 58, 0); "></i>                                
            </a>                     
        </div>

        <div class="leftbox" >
            <i class="far fa-clock" style="font-size: 15px;"></i> October 13, 2008
             &bull; from Maui, Hawaii    
            
            &nbsp; &bull; &nbsp; <i class="fal fa-comment-lines" style="font-size:0.9em"></i> <a href='#Feedback' >49 comments</a>
            
            
        </div>
  
    
      

            
    </div>

    
    


    <div class="clearfix">:P</div>



    <div id="ArticleBody" class="postcontent" 
         itemprop="articleBody" 
         style="position: relative" >
        
        <div class="document-outline" >
            <div class="document-outline-header">On this page:</div>
            <div class="document-outline-content">           
            </div>
            
                
        </div>


        <p>jQuery makes it pretty easy to manipulate client side elements, so it’s not all that often that I think about using something like client side templating to get content loaded into pages. However, recently I have been working on a few apps that had fairly complex list based layouts and it started getting&#160; tedious to use manual code to update all the items. Further doing it by hand can be brittle if your layout changes as you have to keep the layout and the update code in sync.</p>  <p>Templating can address this problem by letting you use templates that describe your output with ‘holes’ that are filled in with data when the template is processed. Templating is a good solution in a few scenarios:</p>  <ul>   <li>Loading all data from the server especially in rich list displays </li>    <li>Adding or updating new items in lists </li>    <li>Anywhere you need to add new complex content to the page </li>    <li>Anything that requires client side HTML rendering </li> </ul>  <p>All of these scenarios have in common that new items are created and these items are injected into the page from the client. </p>  <h3>‘Manual’ Templating</h3>  <p>Templating can take many forms actually and it may not even require an official templating engine to feed data into the page. For example in a number of applications I’ve been using hidden elements that serve as a template into which data is then filled manually via jQuery code. Take this layout for example:</p>  <p><img style="display: inline" title="TemplateManual" border="0" alt="TemplateManual" src="http://www.west-wind.com/Weblog/images/200801/WindowsLiveWriter/ClientTemplatingwithjQuery_31B/TemplateManual_ecf47edd-ea45-450b-b034-95ea464801ca.png" width="726" height="436" /> </p>  <p>which is filled from a ‘manual’ template that looks like this:</p>  <pre class="code"><span style="color: blue">&lt;</span><span style="color: #a31515">div </span><span style="color: red">id</span><span style="color: blue">=&quot;StockItemTemplate&quot; </span><span style="color: red">class</span><span style="color: blue">=&quot;itemtemplate&quot; </span><strong><span style="color: red">style</span><span style="color: blue">=&quot;</span><span style="color: red">display</span>:</strong><span style="color: blue"><strong>none&quot;</strong>&gt;
     &lt;</span><span style="color: #a31515">div </span><span style="color: red">class</span><span style="color: blue">=&quot;stockicon&quot;&gt;&lt;/</span><span style="color: #a31515">div</span><span style="color: blue">&gt;
     &lt;</span><span style="color: #a31515">div </span><span style="color: red">class</span><span style="color: blue">=&quot;itemtools&quot;&gt;
         &lt;</span><span style="color: #a31515">a </span><span style="color: red">href</span><span style="color: blue">='javascript:{}' </span><span style="color: red">class</span><span style="color: blue">=&quot;hoverbutton&quot; <br />            </span><span style="color: red">onclick</span><span style="color: blue">=&quot;DeleteQuote(this.parentNode.parentNode,event);return false;&quot;&gt;<br />         &lt;</span><span style="color: #a31515">img </span><span style="color: red">src</span><span style="color: blue">=&quot;../images/remove.gif&quot; /&gt;&lt;/</span><span style="color: #a31515">a</span><span style="color: blue">&gt;
     &lt;/</span><span style="color: #a31515">div</span><span style="color: blue">&gt;
     &lt;</span><span style="color: #a31515">div </span><span style="color: red">class</span><span style="color: blue">=&quot;itemstockname&quot;&gt;&lt;/</span><span style="color: #a31515">div</span><span style="color: blue">&gt;
     &lt;</span><span style="color: #a31515">div </span><span style="color: red">class</span><span style="color: blue">=&quot;itemdetail&quot;&gt;
         &lt;</span><span style="color: #a31515">table </span><span style="color: red">style</span><span style="color: blue">=&quot;</span><span style="color: red">padding</span>: <span style="color: blue">5px</span>;<span style="color: blue">&quot;&gt;&lt;</span><span style="color: #a31515">tr</span><span style="color: blue">&gt;
             &lt;</span><span style="color: #a31515">td</span><span style="color: blue">&gt;</span>Last Trade:<span style="color: blue">&lt;/</span><span style="color: #a31515">td</span><span style="color: blue">&gt;
             &lt;</span><span style="color: #a31515">td </span><span style="color: red">id</span><span style="color: blue">=&quot;tdLastPrice&quot; </span><span style="color: red">class</span><span style="color: blue">=&quot;stockvaluecolumn&quot;&gt;&lt;/</span><span style="color: #a31515">td</span><span style="color: blue">&gt;
             &lt;</span><span style="color: #a31515">td</span><span style="color: blue">&gt;</span>Qty:<span style="color: blue">&lt;/</span><span style="color: #a31515">td</span><span style="color: blue">&gt;
             &lt;</span><span style="color: #a31515">td </span><span style="color: red">id</span><span style="color: blue">=&quot;tdLastQty&quot; </span><span style="color: red">class</span><span style="color: blue">=&quot;stockvaluecolumn&quot;&gt;&lt;/</span><span style="color: #a31515">td</span><span style="color: blue">&gt;
             &lt;</span><span style="color: #a31515">td</span><span style="color: blue">&gt;</span>Holdings:<span style="color: blue">&lt;/</span><span style="color: #a31515">td</span><span style="color: blue">&gt;
             &lt;</span><span style="color: #a31515">td </span><span style="color: red">id</span><span style="color: blue">=&quot;tdItemValue&quot; </span><span style="color: red">class</span><span style="color: blue">=&quot;stockvaluecolumn&quot;&gt;&lt;/</span><span style="color: #a31515">td</span><span style="color: blue">&gt;                
             &lt;</span><span style="color: #a31515">td </span><span style="color: red">id</span><span style="color: blue">=&quot;tdTradeDate&quot; </span><span style="color: red">colspan</span><span style="color: blue">=&quot;2&quot;&gt;&lt;/</span><span style="color: #a31515">td</span><span style="color: blue">&gt;
         &lt;/</span><span style="color: #a31515">tr</span><span style="color: blue">&gt;&lt;/</span><span style="color: #a31515">table</span><span style="color: blue">&gt;
     &lt;/</span><span style="color: #a31515">div</span><span style="color: blue">&gt;
&lt;/</span><span style="color: #a31515">div</span><span style="color: blue">&gt;</span></pre>
</a>

<p>The ‘template’ is a single <em>hidden</em> element in the page that is the empty layout of each of the template items that is loaded without any data applied to it. When the items are loaded from the server via an AJAX callback an array of Stock items are retrieved and they are then merged via code that finds each element and assigns the value.</p>
</a>

<pre class="code"><span style="color: blue">function </span>LoadQuotes()
{
    <span style="color: blue">if </span>(!userToken)
        <span style="color: blue">return</span>;   <span style="color: green">// *** not logged in        
        
    </span>proxy.invoke(<span style="color: teal">&quot;GetPortfolioItems&quot;</span>,
         {userToken: userToken },
         <span style="color: blue">function</span>( message ) {            
            $(<span style="color: teal">&quot;#lstPortfolioContainer&quot;</span>).empty();
            
            $.each( message.Items,<span style="color: blue">function</span>(i) 
            {
                <span style="color: blue">var </span>item = <span style="color: blue">this</span>;   <span style="color: green">// this is the iterated item!
                
                // *** Create a new node from the template by cloning
<strong>                </strong></span><strong><span style="color: blue">var </span>newEl = $(<span style="color: teal">&quot;#StockItemTemplate&quot;</span>).clone()
                                .attr(<span style="color: teal">&quot;id&quot;</span>,item.Pk + <span style="color: teal">&quot;_STOCK&quot;</span>)
                                .fadeIn(<span style="color: teal">&quot;slow&quot;</span>);                       
</strong>                
                <span style="color: green">// *** dump the data into it
<strong>                </strong></span><strong>UpdatePortfolioItem(newEl,item);                        
</strong>                
                <span style="color: green">// *** Append item to the list view container and hook up click event for detail
                </span>newEl.click(<span style="color: blue">function</span>() { ShowStockEditWindow(newEl); } )    
                     .appendTo(<span style="color: teal">&quot;#lstPortfolioContainer&quot;</span>);
            });

            <span style="color: green">// *** Update totals    
            </span>$(<span style="color: teal">&quot;#spanPortfolioTotal&quot;</span>).text( message.TotalValue.formatNumber(<span style="color: teal">&quot;c&quot;</span>) );
            $(<span style="color: teal">&quot;#divPortfolioCount&quot;</span>).text( message.TotalItems.formatNumber(<span style="color: teal">&quot;f0&quot;</span>) + <span style="color: teal">&quot; items&quot;</span>);
         },
         OnPageError);
}</pre>
</a>

<pre class="code"><span style="color: blue">function </span>UpdatePortfolioItem(jItem,stock)
{                    
    <span style="color: green">// *** Now fill in the stock data 
    </span>jItem.find(<span style="color: teal">&quot;.itemstockname&quot;</span>).text(stock.Symbol + <span style="color: teal">&quot; - &quot; </span>+ stock.Company);        
    jItem.find(<span style="color: teal">&quot;#tdLastPrice&quot;</span>).text(stock.LastPrice.toFixed(2));
    jItem.find(<span style="color: teal">&quot;#tdLastQty&quot;</span>).text(stock.Qty.toFixed(0));
    jItem.find(<span style="color: teal">&quot;#tdItemValue&quot;</span>).text(stock.ItemValue.formatNumber(<span style="color: teal">&quot;c&quot;</span>));
    jItem.find(<span style="color: teal">&quot;#tdTradeDate&quot;</span>).text(<span style="color: teal">&quot;as of: &quot; </span>+ stock.LastDate.formatDate(<span style="color: teal">&quot;MMM dd, hh:mmt&quot;</span>) ); 
}</pre>
</a>

<p>The manual templating works by cloning the template element, assigning a new ID to it, filling it with data and then injecting it into the document in the right place – in this case into the list container.</p>

<p>This is a code centric approach and it’s pretty straight forward albeit a bit tedious and as mentioned potentially brittle if the template is changed.</p>

<p><strong>Copy and Fill Templating</strong></p>

<p>A similar approach that doesn’t require a separate template can be used if you need to add or update items in a list. Rather than cloning an empty template that is separately loaded into the page (and which some Html purists would complain about for document clarity) you can pick up an existing item on the page and duplicate it. </p>

<p>So in the example above instead of cloning a template I can select the first div tag and clone it:</p>

<pre class="code"><span style="color: blue">var </span>newEl = $(<span style="color: teal">&quot;#lstPortfolioContainer&gt;div:first-child&quot;</span>).clone()
                .attr(<span style="color: teal">&quot;id&quot;</span>, item.Pk + <span style="color: teal">&quot;_STOCK&quot;</span>);
     </pre>
</a>

<p>This works as long you start off with existing content and you’re guaranteed that SOME content exists to clone from.</p>

<p>In the example above this wouldn’t work because the list renders initially empty and is filled from the client, but the copy and fill can work well in forms where you add new items or update existing ones and avoids any template embedding into the page. This can be especially useful for ASP.NET applications that fill lists with data server side and you only need to update or add items. </p>

<p>Although this approach doesn’t work for everything, when it does work it can be a great time saver because you don’t have to duplicate anything as you are simply reusing what was already rendered server side. I’ve used this frequently for client side updates of grids for example.</p>

<h3>jTemplates</h3>

<p>Manual embedding works, but as you can see it can be a bit tedious to find and update each item</p>

<p>There are few template engines available for jQuery and the one I’ve used for a while is <a target="_blank" href="http://jtemplates.tpython.com/">jTemplates</a>. jTemplates is fairly easy to use and it works reliably, although I have to say that I’m not a fan of the python like template syntax. But it works and is fairly powerful in terms of what you can accomplish. jTemplates work by taking the template and turning it into Javascript code that gets executed which means that template placeholder items can include expressions that reference other code.</p>

<p>Let’s look at another example. Here’s a form that’s my admin form my currently reading list on this blog. The list is originally rendered with a ListView control on the server. I can then go in and add or edit items which pops up ‘dialog’ ontop of the existing content:</p>

<p><img style="display: inline" title="jtemplate1" border="0" alt="jtemplate1" src="http://www.west-wind.com/Weblog/images/200801/WindowsLiveWriter/ClientTemplatingwithjQuery_31B/jtemplate1_d04685e1-3387-4556-975f-874a13154bc8.png" width="719" height="553" /> </p>

<p>When the Save button is clicked the book list is updated or a new item added using jTemplates. The jTemplates template for an individual item looks like this:</p>

<pre class="code"><span style="color: blue">&lt;</span><span style="color: #a31515">script </span><span style="color: red">type</span><span style="color: blue">=&quot;text/html&quot; </span><span style="color: red">id</span><span style="color: blue">=&quot;item_template&quot;&gt;    
    </span>&lt;div style=&quot;float: right;&quot; id=&quot;divBookOptions&quot;&gt;
        &lt;a href=&quot;javascript:void(0);&quot; onclick=&quot;removeBook(this,event);return false;&quot; &gt;
            &lt;img border=&quot;0&quot; src=&quot;../images/remove.gif&quot; class=&quot;hoverbutton&quot;/&gt;
        &lt;/a&gt;
        &lt;br /&gt;
        &lt;small&gt;{$T.SortOrder}&lt;/small&gt;
    &lt;/div&gt;
    &lt;img src=&quot;{$T.AmazonImage}&quot; id=&quot;imgAmazon&quot;/&gt;
    &lt;b&gt;&lt;a href=&quot;{$T.AmazonUrl}&quot; target=&quot;_blank&quot; /&gt;{$T.Title}&lt;/b&gt;
    &lt;br/&gt;
    &lt;small&gt;{$T.Author}&lt;/small&gt;
    &lt;br/&gt;
    {#if $T.Highlight}&lt;small&gt;&lt;i&gt;Highlighted&lt;/i&gt;&lt;/small&gt;{#/if}   
<span style="color: blue">&lt;/</span><span style="color: #a31515">script</span><span style="color: blue">&gt;</span></pre>
</a>

<p>Note the little trick of using a &lt;script type=”text/html”&gt; which is a little gimme from John Resig that allows hiding any markup in the document without interfering with HTML validators. The script can be accessed by its ID and the content retrieved using the .html():</p>

<pre class="code"><span style="color: blue">var </span>template = $(<span style="color: teal">&quot;#item_template&quot;</span>).html();</pre>

<p></a>which gives you the template text. This is a useful trick for all sorts of things that you might want to embed into an HTML document as Data.</p>

<p>jTemplates uses python like syntax and the $T is a reference to the data item passed to the template. Data can be passed in as an object and you can reference the object’s properties off this $T data item. </p>

<p>Here’s the code that is used to save or add a new book to the list (both updating the server database and the list on screen):</p>

<p><font size="2"><font face="Courier New"><span style="color: blue">function </span>saveBook(ctl) 

      <br />{ 

      <br />&#160;&#160;&#160; <span style="color: blue">var </span>jItem = $(ctl); 

      <br />&#160;&#160;&#160; <br />&#160;&#160;&#160; <span style="color: blue">var </span>book = activeBook; 

      <br />

      <br />&#160;&#160;&#160; </font></font><font size="2"><font face="Courier New"><span style="color: green">// must assign the Pk reference 
        <br />&#160;&#160; </span>book.Pk = bookPk; 

      <br />&#160;&#160;&#160; <span style="color: blue">if</span>(bookPk &lt; 1) 

      <br />&#160;&#160;&#160;&#160;&#160;&#160; bookPk = -1; 

      <br />&#160;&#160;&#160; <br />&#160;&#160;&#160; book.Title = $(<span style="color: teal">&quot;#&quot;</span>+ scriptVars.txtTitleId).val(); 

      <br />&#160;&#160;&#160; book.Author = $(<span style="color: teal">&quot;#&quot;</span>+ scriptVars.txtAuthorId).val(); 

      <br />&#160;&#160;&#160; book.AmazonUrl =&#160; $(<span style="color: teal">&quot;#&quot;</span>+ scriptVars.txtAmazonUrlId).val(); 

      <br />&#160;&#160;&#160; book.AmazonImage = $(<span style="color: teal">&quot;#&quot;</span>+ scriptVars.txtAmazonImageId).val(); 

      <br />&#160;&#160;&#160; book.SortOrder = $(<span style="color: teal">&quot;#&quot;</span>+ scriptVars.txtSortOrderId).val(); 

      <br />&#160;&#160;&#160; book.Highlight = $(<span style="color: teal">&quot;#&quot;</span>+ scriptVars.chkHighlightId).attr(<span style="color: teal">&quot;checked&quot;</span>); 

      <br />&#160;&#160;&#160; book.Category = $(<span style="color: teal">&quot;#&quot;</span>+ scriptVars.txtBookCategoryId).val(); 

      <br />&#160;&#160;&#160; <br />&#160;&#160;&#160; <span style="color: blue">if</span>(book.SortOrder) 

      <br />&#160;&#160;&#160;&#160;&#160;&#160;&#160; book.SortOrder = parseInt( book.SortOrder); 

      <br />&#160;&#160;&#160; <br />&#160;&#160;&#160; showProgress(); 

      <br />&#160;&#160;&#160; Proxy.SaveBook(book, 

      <br />&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; <span style="color: blue">function</span>(savedPk) 

      <br />&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; { 

      <br />&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; showProgress(<span style="color: blue">true</span>); 

      <br />&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; showStatus(<span style="color: teal">&quot;Book has been saved.&quot;</span>,5000); 

      <br />&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; panEditBook.hide(); 

      <br />&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; <br />&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; book.Pk = savedPk; 

      <br /><strong>&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; updateBook(book);</strong> 

      <br />&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; }, 

      <br />&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160; onPageError); 

      <br />}</font></font></p>
</a>

<pre class="code"><span style="color: blue">function </span>updateBook(book)
{    
<span style="color: blue">   var </span>item = $(<span style="color: teal">&quot;.bookitem[@tag=&quot; </span>+book.Pk +<span style="color: teal">&quot;]&quot;</span>);    
      
   <span style="color: blue">if </span>(item.length &lt; 1) {
        <span style="color: green">// create a new item wrapper to merge template inside of
        </span>item = $(<span style="color: teal">&quot;&lt;div id='divBookItem' class='bookitem' tag='&quot; </span>+ book.Pk + <span style="color: teal">&quot;' onclick='editBook(this);'&gt;&lt;/div&gt;&quot;</span>)
                    .appendTo(<span style="color: teal">&quot;#divBookListWrapper&quot;</span>);
    }
                     
 <strong>   <span style="color: blue">var </span>template = $(<span style="color: teal">&quot;#item_template&quot;</span>).html();
</strong>    
<strong>    item.setTemplate(template);</strong>
<strong>    item.processTemplate(book);
</strong>}</pre>

<p>The first function pulls the captured input values into a book object which is then submitted to the server. On the callback – when it worked – the book is updated and the list item is updated by calling the updateBook function. Inside that callback the existing item is selected or a new ‘wrapper’ created. Then the template is applied against that item and the parsed template replaces the inner content of the item selected. IOW, the template becomes the innerHTML.</p>

<p>The above is a simple example of a template that’s just a single item, but jTemplates also supports iteration over items as well as the ability to do some limited structured statements.</p>

<p>Here’s another example that’s used to render items from the Amazon Web Service which returns an array of Amazon items to the client:</p>

<pre class="code"><span style="color: blue">&lt;</span><span style="color: #a31515">script </span><span style="color: red">type</span><span style="color: blue">=&quot;text/html&quot; </span><span style="color: red">id</span><span style="color: blue">=&quot;amazon_item_template&quot;&gt;    
</span>{#foreach $T.Rows as row}
&lt;div class=&quot;amazonitem&quot; ondblclick=&quot;selectBook(this);&quot; tag=&quot;{$T.row.Id}&quot;&gt;
    &lt;img src=&quot;{$T.row.SmallImageUrl}&quot; style=&quot;float: left; margin-right: 10px;&quot; /&gt;
    &lt;div&gt;&lt;b&gt;{$T.row.Title}&lt;/b&gt;&lt;/div&gt;
    &lt;div&gt;&lt;i&gt;{$T.row.Publisher} &amp;nbsp; ({$T.row.PublicationDate})&lt;/i&gt;&lt;/div&gt;
    &lt;small&gt;{$T.row.Author}&lt;/small&gt;
&lt;/div&gt;
{#/for}
<span style="color: blue">&lt;/</span><span style="color: #a31515">script</span><span style="color: blue">&gt;</span></pre>

<p>which is hooked up with code like this:</p>

<pre class="code"><span style="color: blue">function </span>showAmazonList()
{
    panBookList_DragBehavior.show();
    <span style="color: blue">var </span>search = $(<span style="color: teal">&quot;#txtSearchBooks&quot;</span>).val();
    <span style="color: blue">if </span>(!search)
       <span style="color: blue">return</span>;
                                        
    showProgress();        
    Proxy.GetAmazonItems( search,
                          $(<span style="color: teal">&quot;#&quot; </span>+ scriptVars.radSearchTypeId + <span style="color: teal">&quot; input:checked&quot;</span>).val(),
                          $(<span style="color: teal">&quot;#&quot; </span>+ scriptVars.txtAmazonGroupId).val(),
                          <span style="color: blue">function</span>(<strong>matches</strong>) {
                                                    
                            showProgress(<span style="color: blue">true</span>);
                            bookList = matches;
<strong>                            <span style="color: blue">var </span>item = $(<span style="color: teal">&quot;#divBookList_Content&quot;</span>);      </strong>           
                  
<strong>                            item.setTemplate(  $(<span style="color: teal">&quot;#amazon_item_template&quot;</span>).html() );
                            item.processTemplate(matches);                            
</strong>                          },
                          onPageError);
}</pre>

<p>Matches in this case is an array of AmazonLookupItems which looks like this:</p>

<pre class="code">[<span style="color: #2b91af">CallbackMethod</span>]
<span style="color: blue">public </span><span style="color: #2b91af">List</span>&lt;<span style="color: #2b91af">AmazonSearchResult</span>&gt; GetAmazonItems(<span style="color: blue">string </span>search, <span style="color: blue">string </span>type, <span style="color: blue">string </span>amazonGroup)
{
    <span style="color: #2b91af">AmazonLookup </span>lookup = <span style="color: blue">new </span><span style="color: #2b91af">AmazonLookup</span>();
    <span style="color: #2b91af">List</span>&lt;<span style="color: #2b91af">AmazonSearchResult</span>&gt; result = lookup.SearchForBook(
            (type == <span style="color: teal">&quot;Title&quot;</span>) ?
                Amazon.<span style="color: #2b91af">AmazonLookup</span>.<span style="color: #2b91af">SearchCriteria</span>.Title :
                Amazon.<span style="color: #2b91af">AmazonLookup</span>.<span style="color: #2b91af">SearchCriteria</span>.Author,
            search,
            amazonGroup);

    <span style="color: green">//result[0].

    </span><span style="color: blue">return </span>result;
}</pre>

<p></a>The result is serialized as an array which is what</p>

<pre class="code">{#foreach $T.Rows as row}</pre>

<p>{#/for}</p>

<p>iterates over.</p>

<p>You can also a see an example of the #if construct which allows you to conditionally display content. </p>

<pre class="code">{#<span style="color: blue">if </span>$T.Highlight}&lt;small&gt;&lt;i&gt;Highlighted&lt;/i&gt;&lt;/small&gt;{#/<span style="color: blue">if</span>}   </pre>

<p>jTemplate supports only a few #directives including #if,#foreach,#for,#include,#param,#cycle which are few but admittedly enough for the most&#160; common template scenarios.</p>

<p>I’ve used jTemplates in a few applications and it works well, but the syntax is really not to my liking. I also am not terribly fond of the way the plug-in works and how it assigns content as content. Making a tool like this a jQuery plug-in rather than a class that produces string output or at least allows options for that is one example of overemphasizing the jQuery metaphor.</p>

<h3>John Resig’s Microtemplating engine</h3>

<p>A couple of months ago <a target="_blank" href="http://ejohn.org/blog/javascript-micro-templating/">John Resig posted a tiny little templating engine</a> that is positively tiny. This engine is literally 20 lines of very terse (and yes obtuse) code. Heck I’ve looked at the regex expressions for a while now and I still have not quite figured out what it all does. It’s short enough I can post it here:

  <br />

  <br /><em><font color="#ff0000">Updated code that fixes issue with single quotes (per Neil’s comment below):</font></em></p>

<pre class="code"><span style="color: green">// Simple JavaScript Templating
// John Resig - http://ejohn.org/ - MIT Licensed
</span>(<span style="color: blue">function</span>() {
    <span style="color: blue">var </span>cache = {};

    <span style="color: blue">this</span>.tmpl = <span style="color: blue">function </span>tmpl(str, data) {
        <span style="color: green">// Figure out if we're getting a template, or if we need to
        // load the template - and be sure to cache the result.
        </span><span style="color: blue">var </span>fn = !/\W/.test(str) ?
      cache[str] = cache[str] ||
        tmpl(document.getElementById(str).innerHTML) :

        <span style="color: green">// Generate a reusable function that will serve as a template
        // generator (and which will be cached).
      </span><span style="color: blue">new </span>Function(<span style="color: teal">&quot;obj&quot;</span>,
        <span style="color: teal">&quot;var p=[],print=function(){p.push.apply(p,arguments);};&quot; </span>+

        <span style="color: green">// Introduce the data as local variables using with(){}
        </span><span style="color: teal">&quot;with(obj){p.push('&quot; </span>+

        <span style="color: green">// Convert the template into pure JavaScript<br /></span><em>str.replace(/[\r\t\n]/g, <span style="color: teal">&quot; &quot;</span>)
   .replace(/</em><em><span style="color: teal">'(?=[^%]*%&gt;)/g,&quot;\t&quot;)
   </span>.split(<span style="color: teal">&quot;'&quot;</span>).join(<span style="color: teal">&quot;\\'&quot;</span>)
   .split(<span style="color: teal">&quot;\t&quot;</span>).join(<span style="color: teal">&quot;'&quot;</span>)
   .replace(/&lt;%=(.+?)%&gt;/g, <span style="color: teal">&quot;',$1,'&quot;</span>)
   .split(<span style="color: teal">&quot;&lt;%&quot;</span>).join(<span style="color: teal">&quot;');&quot;</span>)
   .split(<span style="color: teal">&quot;%&gt;&quot;</span>).join(<span style="color: teal">&quot;p.push('&quot;</span>)
   + <span style="color: teal">&quot;');}return p.join('');&quot;</span>);</em></pre>


<pre class="code">
        <span style="color: green">// Provide some basic currying to the user
        </span><span style="color: blue">return </span>data ? fn(data) : fn;
    };
})();</pre>
</a>

<p>Basically it turns a template into a block of JavaScript code and then executes that code. The syntax is ASP style markup using &lt;%= expression %&gt; and &lt;% codeblock %&gt; syntax to handle code embedding.</p>

<p>What’s nice about this approach is that you can utilize any Javascript in the template and you’re not limited to just a few commands. The other thing that’s really nice is that it’s really compact – in fact I’ve integrated it into my own client library with a couple of small modifications. The main change I had to make for myself is that I can’t use &lt;% %&gt; because I’m using the script in another library where &lt;% %&gt; is always evaluated as server side script (note – ASP.NET is fine with the &lt;% %&gt; as long as you put it inside &lt;script type=&quot;text/html&quot;&gt; which the ASP.NET parser is smart enough to leave alone. But even for ASP.NET I prefer to use a different script extension just to be clear. So I ended up using &lt;# #&gt; for delimiters. I also added some error handling so that maybe more error information can be seen if something goes wrong with the template.</p>

<p>Here’s my slightly modified and deconstructed version called parseTemplate:
  <br />

  <br /><em><font color="#ff0000">Updated code that fixes issue with single quotes (per Neil’s comment below in italics):</font></em></p>

<pre class="code"><span style="color: blue">var </span>_tmplCache = {}
<span style="color: blue">this</span>.parseTemplate = <span style="color: blue">function</span>(str, data) {
    <span style="color: green">/// &lt;summary&gt;
    /// Client side template parser that uses &amp;lt;#= #&amp;gt; and &amp;lt;# code #&amp;gt; expressions.
    /// and # # code blocks for template expansion.
    /// NOTE: chokes on single quotes in the document in some situations
    ///       use &amp;amp;rsquo; for literals in text and avoid any single quote
    ///       attribute delimiters.
    /// &lt;/summary&gt;    
    /// &lt;param name=&quot;str&quot; type=&quot;string&quot;&gt;The text of the template to expand&lt;/param&gt;    
    /// &lt;param name=&quot;data&quot; type=&quot;var&quot;&gt;
    /// Any data that is to be merged. Pass an object and
    /// that object's properties are visible as variables.
    /// &lt;/param&gt;    
    /// &lt;returns type=&quot;string&quot; /&gt;  
    </span><span style="color: blue">var </span>err = <span style="color: teal">&quot;&quot;</span>;
    <span style="color: blue">try </span>{
        <span style="color: blue">var </span>func = _tmplCache[str];
        <span style="color: blue">if </span>(!func) {
            <span style="color: blue">var </span>strFunc =
            <span style="color: teal">&quot;var p=[],print=function(){p.push.apply(p,arguments);};&quot; </span>+
                        <span style="color: teal">&quot;with(obj){p.push('&quot; </span>+
            <span style="color: green">//                        str
            //                  .replace(/[\r\t\n]/g, &quot; &quot;)
            //                  .split(&quot;&lt;#&quot;).join(&quot;\t&quot;)
            //                  .replace(/((^|#&gt;)[^\t]*)'/g, &quot;$1\r&quot;)
            //                  .replace(/\t=(.*?)#&gt;/g, &quot;',$1,'&quot;)
            //                  .split(&quot;\t&quot;).join(&quot;');&quot;)
            //                  .split(&quot;#&gt;&quot;).join(&quot;p.push('&quot;)
            //                  .split(&quot;\r&quot;).join(&quot;\\'&quot;) + &quot;');}return p.join('');&quot;;

            </span>str.replace(/[\r\t\n]/g, <span style="color: teal">&quot; &quot;</span>)
               .replace(/<span style="color: teal">'(?=[^#]*#&gt;)/g, &quot;\t&quot;)
               </span>.split(<span style="color: teal">&quot;'&quot;</span>).join(<span style="color: teal">&quot;\\'&quot;</span>)
               .split(<span style="color: teal">&quot;\t&quot;</span>).join(<span style="color: teal">&quot;'&quot;</span>)
               .replace(/&lt;#=(.+?)#&gt;/g, <span style="color: teal">&quot;',$1,'&quot;</span>)
               .split(<span style="color: teal">&quot;&lt;#&quot;</span>).join(<span style="color: teal">&quot;');&quot;</span>)
               .split(<span style="color: teal">&quot;#&gt;&quot;</span>).join(<span style="color: teal">&quot;p.push('&quot;</span>)
               + <span style="color: teal">&quot;');}return p.join('');&quot;</span>;

            <span style="color: green">//alert(strFunc);
            </span>func = <span style="color: blue">new </span>Function(<span style="color: teal">&quot;obj&quot;</span>, strFunc);
            _tmplCache[str] = func;
        }
        <span style="color: blue">return </span>func(data);
    } <span style="color: blue">catch </span>(e) { err = e.message; }
    <span style="color: blue">return </span><span style="color: teal">&quot;&lt; # ERROR: &quot; </span>+ err.htmlEncode() + <span style="color: teal">&quot; # &gt;&quot;</span>;
}</pre>
</a>

<p>If you want to see what the generated code looks like uncomment the alert line – you’ll find that the template is deconstructed into a string of Javascript code that is then created as function and executed with the data parameter as an argument. The data parameter becomes the context of the call so that the properties of the object are available. </p>

<p>This is sort of the same approach like WCF uses of a ‘wrapped’ object&#160; - to pass multiple parameters you can simply create a map:</p>

<pre class="code">parseTemplate($(<span style="color: teal">&quot;#ItemTemplate&quot;).html()</span>,<br />              { name: &quot;rick&quot;, address: { street: &quot;32 kaiea&quot;, city: &quot;paia&quot;} } );</pre>

<p>where a template might be:</p>

<pre class="code"><span style="color: blue">&lt;</span><span style="color: #a31515">script </span><span style="color: red">id</span><span style="color: blue">=&quot;ItemTemplate&quot; </span><span style="color: red">type</span><span style="color: blue">=&quot;text/html&quot;&gt;
 </span>&lt;div&gt;
    &lt;div&gt;&lt;#= name #&gt;&lt;/div&gt;
    &lt;div&gt;&lt;#= address.street #&gt;&lt;/div&gt;
 &lt;/div&gt;
<span style="color: blue">&lt;/</span><span style="color: #a31515">script</span><span style="color: blue">&gt;</span></pre>

<p>You can also loop through a list of items by using code blocks. Imagine you got a list of stocks returned as an array as I showed earlier. You can then do:</p>

<pre class="code"><span style="color: blue">var </span>s = parseTemplate($(<span style="color: teal">&quot;#StockListTemplate&quot;</span>).html(), { stocks: message.listresult.Rows } );</pre>
</a></a>

<p>which is then applied against this template</p>

<pre class="code"> <span style="color: blue">&lt;</span><span style="color: #a31515">script </span><span style="color: red">id</span><span style="color: blue">=&quot;StockListTemplate&quot; </span><span style="color: red">type</span><span style="color: blue">=&quot;text/html&quot;&gt;
 </span>&lt;# for(var i=0; i &lt; stocks.length; i++)     <br />    {         <br />         var stock = stocks[i]; #&gt;
 &lt;div&gt;
    &lt;div&gt;&lt;#= stock.company  #&gt; ( &lt;#= stock.symbol #&gt;)&lt;/div&gt;
    &lt;div&gt;&lt;#= stock.lastprice.formatNumber(&quot;c&quot;) #&gt;&lt;/div&gt;
 &lt;/div&gt;
 &lt;# } #&gt;
<span style="color: blue">&lt;/</span><span style="color: #a31515">script</span><span style="color: blue">&gt;</span></pre>

<p>Effectively any child properties of the passed object are available as variables in the template courtesy of the with() construct in the generated Javascript code.</p>

<p>Personally I prefer to do scripting this way to what jTemplates does simply because you effectively have access to full Javascript functionality in the template. It’s also a more familiar approach if you’ve used any sort of ASP.NET scripting.</p>

<p>To put this in perspective here’s the first example where I manually loaded up the stock template replaced with the parseTemplate approach. In this example I use a single item template and use code to loop through list rather than having the template do it</p>

<p>The following is a script template similar to the stock template in the first example:</p>

<pre class="code"><span style="color: blue">&lt;</span><span style="color: #a31515">script </span><span style="color: red">id</span><span style="color: blue">=&quot;ItemTemplate&quot; </span><span style="color: red">type</span><span style="color: blue">=&quot;text/html&quot;&gt;
</span>&lt;# for(var i=0; i &lt; stocks.length; i++)     
   {         
     var stock = stocks[i]; 
 #&gt;
 &lt;div id=&quot;&lt;#= stock.pk #&gt;_STOCK&quot; class=&quot;itemtemplate&quot; style=&quot;display:none&quot; onclick=&quot;ShowStockEditWindow(this);&quot;&gt;
     &lt;div class=&quot;stockicon&quot;&gt;&lt;/div&gt;
     &lt;div class=&quot;itemtools&quot;&gt;
         &lt;a href=&quot;javascript:{}&quot; class=&quot;hoverbutton&quot; onclick=&quot;DeleteQuote(this.parentNode.parentNode,event);return false;&quot;&gt;
         &lt;img src=&quot;../images/remove.gif&quot; /&gt;&lt;/a&gt;
     &lt;/div&gt;
     &lt;div class=&quot;itemstockname&quot;&gt;&lt;#= stock.symbol #&gt; - &lt;#= stock.company #&gt;&lt;/div&gt;        
     &lt;div class=&quot;itemdetail&quot;&gt;
         &lt;table style=&quot;padding: 5px;&quot;&gt;&lt;tr&gt;
             &lt;td&gt;Last Trade:&lt;/td&gt;
             &lt;td id=&quot;tdLastPrice&quot; class=&quot;stockvaluecolumn&quot;&gt;&lt;#= stock.lastprice.toFixed(2) #&gt;&lt;/td&gt;
             &lt;td&gt;Qty:&lt;/td&gt;
             &lt;td id=&quot;tdLastQty&quot; class=&quot;stockvaluecolumn&quot;&gt;&lt;#= stock.qty #&gt;&lt;/td&gt;
             &lt;td&gt;Holdings:&lt;/td&gt;
             &lt;td id=&quot;tdItemValue&quot; class=&quot;stockvaluecolumn&quot;&gt;&lt;#= stock.itemvalue.formatNumber(&quot;c&quot;) #&gt;&lt;/td&gt;                
             &lt;td id=&quot;tdTradeDate&quot; colspan=&quot;2&quot;&gt;&lt;#= stock.lastdate.formatDate(&quot;MMM dd, hh:mmt&quot;) #&gt;&lt;/td&gt;
         &lt;/tr&gt;&lt;/table&gt;
     &lt;/div&gt;
&lt;/div&gt;
&lt;# } #&gt;
<span style="color: blue">&lt;/</span><span style="color: #a31515">script</span><span style="color: blue">&gt;</span></pre>
</a></a></a>

<p>In this example the template is loaded either individually or&#160; updated in a loop to load all quotes:</p>

<pre class="code"><span style="color: blue">function </span>LoadQuotes()
{
    Proxy.callMethod(<span style="color: teal">&quot;GetPortfolioItems&quot;</span>,
         [],
         <span style="color: blue">function</span>(message) {
             $(<span style="color: teal">&quot;#lstPortfolioContainer&quot;</span>).empty();

             <span style="color: blue">if </span>(!message)
                 <span style="color: blue">return</span>;

             <span style="color: blue">if </span>(message.listresult) {

<strong>                 </strong><strong><span style="color: green">// Parse template with stock rows as array input
                 </span><span style="color: blue">var </span>html = parseTemplate($(<span style="color: teal">&quot;#ItemTemplate&quot;</span>).html(), 
                                         { stocks: message.listresult.Rows });
                 $(html).fadeIn(<span style="color: teal">&quot;slow&quot;</span>)
                        .appendTo(<span style="color: teal">&quot;#lstPortfolioContainer&quot;</span>);
</strong>             }

             <span style="color: green">// *** Update totals    
             </span>$(<span style="color: teal">&quot;#spanPortfolioTotal&quot;</span>).text(message.totalvalue.formatNumber(<span style="color: teal">&quot;c&quot;</span>));
             $(<span style="color: teal">&quot;#divPortfolioCount&quot;</span>).text(message.totalitems.formatNumber(<span style="color: teal">&quot;f0&quot;</span>) + <span style="color: teal">&quot; items&quot;</span>);
         },
         OnPageError);
}</pre>

<p>As you can see the Javascript code has been reduced significantly and the template – to me at least – is very easy to parse understand modify.</p>

<p><strong>A problem with Single Quotes</strong></p>

<p>As nice as the MicroTemplating engine is there is one problem: The parser has problems with templates that contain single quotes as literal text in some cases. The RegEx expression tries to allow for single quotes and it does in some cases work. But if you use single quotes to wrap attribute values the parser will fail with an ugly string error in the parseTemplate function because the single quote will end up as the delimiter for the function string resulting in invalid Javascript code to evaluate.</p>

<p>While this isn’t a big issue since it should be easy to avoid single quotes in markup and you can use &amp;rsqutoe; for quote literals in HTML markup it’s still a bit inconsistent.</p>

<p><em><font color="#ff0000">Updated code that fixes issue with single quotes (per Neil’s comment below)</font></em></p>

<h3>Other Javascript Templating</h3>

<p>Microsoft is also at it again as well with a new client template&#160; engine slated for Version 4.0 of ASP.NET. MS&#160; originally had client side templates in ATLAS which were later pulled – a good thing this didn’t make it because the XML based markup script was painful to work with with a hideous repetitious and self referencing model that was confusing as hell. The new template engine looks a lot cleaner and is bound and generally follows the same principles that I’ve shown above with jTemplates or the John Resig’s MicroTemplate parser. </p>

<p><a target="_blank" href="http://www.encosia.com/">Dave Ward</a> has a <a target="_blank" href="http://encosia.com/2008/07/23/sneak-peak-aspnet-ajax-4-client-side-templating/">great blog post with a sample</a> that shows the basics of client templates and Bertrand also has an <a target="_blank" href="http://msdn.microsoft.com/en-us/magazine/cc972638.aspx">article on these templates in MSDN</a> in the current issue. BTW, if you like the jQuery content here make sure you also subscribe to <a target="_blank" href="http://feeds.encosia.com/Encosia">Dave’s RSS feed</a> – he’s got some kick ass content on jQuery and ASP.NET.</p>

<p>I haven’t had a chance to look at this stuff other than reading through the articles. While I think this stuff looks very promising I can’t I’m too excited about it – mainly because it still relies on the rest of the Microsoft Client Library. Just to do scripting that’s a bit much of a hit especially when I already have alternatives in this space. But if you’re already using ASP.NET AJAX then the new features are a natural path to take.</p>

<h3>Client Templating – Great for pure Client Implementations</h3>

<p>I’m glad to see that there are a few templating solutions available. Templating makes creating pure client interfacing much easier and it brings some of the concepts that you might be familiar with ASP.NET server applications closer to home. After all things like the ListView, Repeater and other repeating controls are essentially template engines and many similar concepts are used in the client engines. Templates make it possible to let you render rich UI on the client without server markup and yet still let you keep the markup in one place (the template) and even lets you edit the content in your favorite designer if you choose to place the template into a regular document element that maybe is merely invisible.</p>

<p>Personally I like the approach of the MicroTemplate best because it’s dead simple and hits the right notes for me. I don’t need a fancy templating language if I can use JavaScript in my template to perform simple structural blocks and looping. No need to learn some funky template markup language but rather use what you know.</p>



        
        <div style="margin: 30px 0">
            <h3 style="margin-top: 30px">Other Posts you might also like
            </h3>

            <ul>
                
                        <li><a href="https://weblog.west-wind.com/posts/2009/Aug/31/Getting-and-setting-max-zIndex-with-jQuery">Getting and setting max zIndex with jQuery</a></li>
                    
                        <li><a href="https://weblog.west-wind.com/posts/2013/Oct/09/Prefilling-an-SMS-on-Mobile-Devices-with-the-sms-Uri-Scheme">Prefilling an SMS on Mobile Devices with the sms: Uri Scheme</a></li>
                    
                        <li><a href="https://weblog.west-wind.com/posts/2014/Oct/24/AngularJs-and-Promises-with-the-http-Service">AngularJs and Promises with the $http Service</a></li>
                    
                        <li><a href="https://weblog.west-wind.com/posts/2025/May/10/Lazy-Loading-the-Mermaid-Diagram-Library">Lazy Loading the Mermaid Diagram Library</a></li>
                    
            </ul>
        </div>
        
    </div>
    
       

    <div class="advert">                  
        
        

    </div>
    
  
   <div class='borderbox'>
       
       <div id="CategoryHeader">
            <div class="share-box">
                <span class="share-label">Share on:</span>   
                                   
                <a href="https://twitter.com/intent/tweet?url=https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery&amp;text=Client Templating with jQuery&amp;via=RickStrahl" target="_blank" title="Tweet on Twitter">
                    <span class="fa-stack" style="line-height: 1em; width: 1em; height: 1em;">
                        <i class="fas fa-square fa-stack-1x" style="color: white; font-size: 0.95em"></i>
                        <i class="fab fa-twitter-square fa-stack-1x" style="color: rgb(53, 155, 240);"></i>
                    </span>
                </a>               

              

                <a href="https://www.facebook.com/sharer/sharer.php?u=https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery&amp;t=Client Templating with jQuery" target="_blank" title="Share on Facebook">
                        <span class="fa-stack" style="line-height: 1em; width: 1em; height: 1em;">
                            <i class="fas fa-square fa-stack-1x" style="color: white; font-size: 0.96em"></i>
                            <i class="fab fa-facebook-square fa-stack-1x" style=" color: rgb(63, 96, 170); "></i>                
                        </span>
                </a>
                <a href="http://www.reddit.com/submit?url=https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery&amp;title=Client Templating with jQuery" target="_blank" title="Submit to Reddit">
                        <span class="fa-stack" style="line-height: 1em; width: 1em; height: 1em;">
                        <i class="fas fa-square fa-stack-1x" style="color: white; font-size: 0.95em"></i>
                            <i class="fab fa-reddit-square fa-stack-1x" style=" color: rgb(245, 58, 0); "></i>                
                            </span>
                </a>                   
            </div>

           
           <div class="donate-buttons">

               

               <div style="flex: none;">
                   <img onclick="window.open('https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=rstrahl@west-wind.com&amp;item_name=Rick+Strahl%27s+Web+Log&amp;no_shipping=0&amp;no_note=1&amp;tax=0&amp;currency_code=USD&amp;lc=US&amp;bn=PP%2dDonationsBF&amp;charset=UTF%2d8&amp;image_url=https://weblog.west-wind.com/images/WebLogBannerLogo.jpg','PayPal');"
                       title="Find this content useful? Consider making a small donation."
                       alt="Make Donation"                                              
                       src="/images/donation.png" />
               </div>
               <div style="font-size: 0.8em; width: 210px;margin-left: 10px" class="hidable-xs">
                   Is this content useful to you? <b>Consider making a small donation</b> to show your support.               
               </div>
           </div>
           
       </div>
       

       <div style="margin-top: 20px">
        <div>Posted in <b><a href='/ShowPosts.aspx?Category=jQuery'>jQuery</a>&nbsp;&nbsp;<a href='/ShowPosts.aspx?Category=JavaScript'>JavaScript</a>&nbsp;&nbsp;</b></div>
</small>  
       </div>

    </div>


    <br />
    
    
    <h2>The Voices of Reason</h2>        
    <hr style="margin-bottom: 30px;"/>

        <div id="ctl00_Content_FeedbackPanel">
	        
        <a name="Feedback">&nbsp;</a>            
        <div class="clearfix"></div>
        
            
            
               <div id="cmt_509277" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=f1bf1c7c0b0d30fb7a9b737ab5b4cad0&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.morningz.com" target="_WebLog" >Stephen</a>
                           <br />
                           October 13, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="509277" href="#509277">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Just to provide yet another option for others to look into<br /><br />jQuery &quot;Chain&quot;<br /><br /><a href='http://ajaxian.com/archives/chainjs-jquery-data-binding-service'>http://ajaxian.com/archives/chainjs-jquery-data-binding-service</a><br /><br />I've been using it for a few weeks and dig it
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_509625" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=fc7253276960c6f0718688e330bca47f&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://webdevdotnet.blogspot.com" target="_WebLog" >Elijah Manor</a>
                           <br />
                           October 14, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="509625" href="#509625">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Good stuff! I can see myself doing some of this stuff now that I'm getting deep into ASP.NET MVC.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_509723" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=cd93483c1bb616efb5480b639dbb9ca3&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://aaron.codebetter.com" target="_WebLog" >Aaron Jensen</a>
                           <br />
                           October 14, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="509723" href="#509723">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Look into spark.. spark provides a story for a single template that can be rendered on either the server or the client. *that* is power.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_510693" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=e9c895a8e449235ef5504ac9c81790ea&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Chris Haines
                           <br />
                           October 15, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="510693" href="#510693">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Have you tried using &lt;#= &quot;'&quot; #&gt; for the single quote issue?<br /><br />(That's a single quote inside double quotes)
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_510770" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           little p
                           <br />
                           October 15, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="510770" href="#510770">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Just a word of warning, W3C standards state that the element attribute 'id' should be unique within a document.  Your templating example uses ids and that could potentially lead to problems depending on how various browsers handle cloned templates with conflicting ids.  Since you're passing around a jquery object referencing a cloned instance you should be able to use class names instead.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_510983" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=84a8c9e83873e69a039f53b66b54517c&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Stephen
                           <br />
                           October 15, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="510983" href="#510983">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           First off, thanks for the blog.  Long time reader, I enjoy your approaches to problems.<br /><br />If you're looking for a good, natural client-side templating solution check out:<br /><br />    <a href='http://jsonfx.net'>http://jsonfx.net</a><br /><br />It supports a standard ASP/JSP style syntax and compiles the template to standard JavaScript at *build-time*, meaning that the template parsing doesn't need to occur in the browser.  It is fast and allows a familiar syntax without jumping through a lot of odd hoops.<br /><br />JsonFx can be used piece-wise or as a complete Ajax solution solving many other problems like JSON-RPC, CSS/JS compaction and client-side localization.<br /><br />Check out the demonstration at: <a href='http://starterkit.jsonfx.net'>http://starterkit.jsonfx.net</a>
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_511308" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://asheeshsoni.blogspot.com/" target="_WebLog" >Asheesh Soni</a>
                           <br />
                           October 15, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="511308" href="#511308">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Last attempt....<br /><br />Great article (as always!)<br />Personally, I'd rather wait for ASP.Net Ajax 4.0 RTW before using client templates (&lt;a href=&quot;http://ajaxpatterns.org/Browser-Side_Templating&quot;&gt;The Browser-Side Templating Pattern&lt;/a&gt;) in production websites.<br />Meanwhile, I am using a modified version of &lt;a href=&quot;http://weblogs.asp.net/scottgu/archive/2006/10/22/Tip_2F00_Trick_3A00_-Cool-UI-Templating-Technique-to-use-with-ASP.NET-AJAX-for-non_2D00_UpdatePanel-scenarios.aspx&quot;&gt;ScottGu's cool UI templating&lt;/a&gt;. (i.e. using the &lt;a href=&quot;http://ajaxpatterns.org/HTML_Message&quot;&gt;HTML Message Pattern&lt;/a&gt;)<br />My modifications allow me to use javascript / jquery / toolkit extenders in my dynamically injected user controls.<br />The good thing about it is that I can (and do) use the same user controls in server side aspx pages, with full visual support, intellisense etc. from Visual Studio.<br />I'll blog about this implementation on my &lt;a href=&quot;http://asheeshsoni.blogspot.com/&quot;&gt;.Net Development Blog&lt;/a&gt; when I get some time.<br /><br />For more info, refer to the following MSDN Cutting Edge articles:<br />&lt;a href=&quot;http://msdn.microsoft.com/en-au/magazine/cc546561.aspx&quot;&gt;ASP.NET AJAX And Client-Side Templates by Dino Esposito&lt;/a&gt;<br />&lt;a href=&quot;http://msdn.microsoft.com/en-au/magazine/cc699560.aspx&quot;&gt;The HTML Message Pattern by Dino Esposito&lt;/a&gt;<br /><br />Cheers<br />Asheesh Soni
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_522065" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Derek
                           <br />
                           October 26, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="522065" href="#522065">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Hi Rick,<br /><br />One of the data elements I am inserting into a template (eg. &lt;div&gt;{$T.commentText}&lt;/div&gt;) is actual html but it being rendered as the html text instead of the representation of the html, if you know what i'm missing i'd really appreciate your help.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_522095" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Derek
                           <br />
                           October 26, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="522095" href="#522095">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Hi again,<br /><br />Its OK I found the answer in one of Dave Ward's posts.<br /><br />For anyone else having the same problem it is because jTemplates has HTML filtering on by default, to disable it use: <br /><br />.setTemplateURL(&quot;jTemplates/CommentListTemplate.htm&quot;, null, { filter_data: false });<br /><br />Not trying to steal your thunder Rick, I just don't want to be one of those people who say &quot;I fixed it&quot; and then leave the rest of the world wondering.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_532836" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Neil
                           <br />
                           November 05, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="532836" href="#532836">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Rick - regarding John Resig's Simple JavaScript Templating, I fixed the multiple quotes issue by rewriting the splits and replaces. Here's the code, which is a drop in replacement for the &quot;Convert the template...&quot; section. Lines 2-4 are the only fundamentally different lines.<br /><br /><div class='commentcode'><pre class="csharpcode">str.replace(/[\r\t\n]/g, <span class="str">" "</span>)
   .replace(/<span class="str">'(?=[^%]*%&gt;)/g,"\t")
   .split("'</span><span class="str">").join("</span>\\<span class="str">'")
   .split("\t").join("'</span><span class="str">")
   .replace(/&lt;%=(.+?)%&gt;/g, "</span><span class="str">',$1,'</span><span class="str">")
   .split("</span>&lt;%<span class="str">").join("</span><span class="str">');")
   .split("%&gt;").join("p.push('</span><span class="str">")
   + "</span><span class="str">');}return p.join('</span>');");</pre></div>
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_547868" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Chad
                           <br />
                           November 21, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="547868" href="#547868">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Great article.  I've learned a tremendous amount from your site and appreciate all the time and energy you've invested in helping the community.  Thanks!
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_568698" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=4a8cf777c3041e0e1e20c9cd3f0ef06d&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://nerdinacan.com" target="_WebLog" >Mason Houtz</a>
                           <br />
                           December 13, 2008
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="568698" href="#568698">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           This is great.<br /><br />I've been doing this kind of thing for about 4 years with a simple jQuery extension I wrote one day and never really looked at again. I like some of the ideas surfaced here in the various implementations you've demonstrated though.<br /><br />One thing that's really important in any templating engine (I believe) is the ability to associate non-markup data with dom elements along the way. For example, I'm typically stepping through an object hierarchy and I'll need to associate some node of that object structure with some rendered element so that I can have some data to work with later when the user clicks on the doodad I just renderd. A link, for instance.<br /><br />Making sure that your templating engine offers you the chance to assign arbitrary non-rendered data onto individual rendered &quot;nodes&quot; (via the .data() jquery method or some other similar approach) is an important process that a lot of straight text-substitutions can't offer. (Sure you could embed IDs into custom attributes, but what if your data is uglier than just a number?)<br /><br />Anyway, here's my old templating scheme if anybody's interested. If anything, it's a lot simpler than some of the solutions you've presented here, so maybe some of your audience can use it as a stepping-off point to make it all fancy-like for their individual needs.<br /><br />Thanks for a great blog.<br /><br />Plugin<br /><a href='http://nerdinacan.com/resources/js/jquery.stamp.js'>http://nerdinacan.com/resources/js/jquery.stamp.js</a><br /><br />Dependencies (wish I could remember who I stole this from)<br /><a href='http://nerdinacan.com/resources/js/type.of.js'>http://nerdinacan.com/resources/js/type.of.js</a><br /><br /><br />Mason Houtz
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_616950" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=a2eed35eeb3981da17e8d80841d4bdea&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://smallworkarounds.blogspot.com" target="_WebLog" >Aashish Gupta</a>
                           <br />
                           February 04, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="616950" href="#616950">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Using jTemplates in my template code i have attached a button with every row.Now i want to attach some click event handler to this button using jquery.So where will i attach this handler in template htm or tpl page or in the main aspx page on which this template is rendred.<br />If i write it on the main aspx page while the jquery is loaded there is no button.The template only loads when i click a certain link then the webmethod is called and then this template is rendered but the event is not attached to the templates button which is rendered in every row
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_642375" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=a2eed35eeb3981da17e8d80841d4bdea&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://smallworkarounds.blogspot.com" target="_WebLog" >Aashish Gupta</a>
                           <br />
                           February 28, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="642375" href="#642375">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Mission Accomplished <br />Here is a grid with full functionalities i.e paging sorting and filtering<br /><a href='http://smallworkarounds.blogspot.com/2009/02/jquery-aspnet-how-to-implement.html'>http://smallworkarounds.blogspot.com/2009/02/jquery-aspnet-how-to-implement.html</a>
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_730073" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Kev
                           <br />
                           April 20, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="730073" href="#730073">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Hi there,<br /><br />You mention that the .net parser ignores &lt;% %&gt; inside the text/html block. This doesn't happen for me. It picks up on undeclared server variables, for example. So in this case below, there's a server error of &quot;The name 'i' does not exist in the current context&quot;. I know you mention that a hash (#) can be used instead, but why doesn't &quot;%&quot; work as suggested above? VS2008 is being used. Thanks for reading. Nice work.<br /><br /><div class='commentcode'><pre class="csharpcode">&lt;script type=<span class="str">"text/html"</span> id=<span class="str">"thumb_template"</span>&gt;
Line 13:      &lt;ul&gt;
Line 14:      &lt;% <span class="kwrd">for</span>(i=0;i&lt;items.length;i++) { %&gt;
Line 15:           &lt;li&gt;&lt;img src=<span class="str">"&lt;%=items[i].media.m%&gt;"</span> /&gt;&lt;/li&gt;
Line 16:      &lt;% } %&gt;</pre></div>
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_743405" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=ca8ec23f96574b98831a01164c9d99d6&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://kapilla.net" target="_WebLog" >Chris Kapilla</a>
                           <br />
                           April 27, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="743405" href="#743405">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Hi Rick,<br /><br />returning to this post after a number of months, and found it very useful -- thanks.  I was however, puzzled by your two different comments<br /><br />&quot;Personally I prefer to do scripting this way to what jTemplates does simply because you effectively have access to full Javascript functionality in the template.&quot;<br /><br />and <br /><br />&quot;Personally I like the approach of the MicroTemplate best because it&#8217;s dead simple and hits the right notes for me.&quot;<br /><br />Care to elaborate on which one you _really_ prefer?<br /><br />thanks<br /><br />Chris K
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_743752" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=beb7fdd4bcd15e35472a96ab8182f034&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.west-wind.com/" target="_WebLog" >Rick Strahl</a>
                           <br />
                           April 27, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="743752" href="#743752">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           @Chris - I like both approaches actually, but I prefer the micro-template because it's just JavaScript tags and behaves more like you'd expect a simple ASP like template engine to work. Plus it's tiny so it's easy to integrate into other tools and I have integrated it into my core client library so it's always available. <br /><br />So to answer your question: It's clearly micro-templating. :-}
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_755014" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://kapilla.net" target="_WebLog" >Chris Kapilla</a>
                           <br />
                           May 05, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="755014" href="#755014">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Rick,<br /><br />I was glad to see that you are monitoring these older postings and still responding to them. I have implemented your version of the micro-template and found it works perfectly for me -- and best of all thanks to your 'elucidations' I even understand how it works now -- which upon first glance I didn't think was possible! :-)<br /><br />I have one issue though -- I don't seem to be able to add any JQuery plug-in event handlers to DOM elements that are created via a template. Is there something special one needs to do in order to make that work?<br /><br />thanks in advance,<br /><br />Chris K
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_755026" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=beb7fdd4bcd15e35472a96ab8182f034&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.west-wind.com/" target="_WebLog" >Rick Strahl</a>
                           <br />
                           May 05, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="755026" href="#755026">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           @Chris - I add event handler frequently and that does work for me, so I'm sure it's possible.<br /><br />Here's an example:<br /><a href='http://www.west-wind.com/WestwindWebToolkit/samples/Ajax/AmazonBooks/BooksAdmin.aspx'>http://www.west-wind.com/WestwindWebToolkit/samples/Ajax/AmazonBooks/BooksAdmin.aspx</a><br /><br />You can look at the JavaScript code on that page and look at the UpdateBook method which creates a new item and then attaches click events.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_755059" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Chris Kapilla
                           <br />
                           May 05, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="755059" href="#755059">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           wow that was fast -- you are the best!<br /><br />thanks for the link I will check it out. It's cool they way you make the different aspects of the code all easily viewable.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_766116" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Strx
                           <br />
                           May 14, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="766116" href="#766116">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           This is the best for me; thanks<br /><a href='http://json-template.googlecode.com/svn/trunk/doc/Introducing-JSON-Template.html'>http://json-template.googlecode.com/svn/trunk/doc/Introducing-JSON-Template.html</a>
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_777858" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=832613ff719ee71f3c90c44cf860cf52&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Mark
                           <br />
                           May 25, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="777858" href="#777858">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Rick,<br /><br />Thanks for this post and your site.  I just discovered it and am glad I did.<br /><br />As a side note, I was investigating the micro-template code today and was puzzled by a portion of the following line:<br /><br />&quot;var p=[],print=function(){p.push.apply(p,arguments);};&quot; +<br /><br />I went to John's Resig's actual post to further investigate why the following was included?<br /><br />     print=function(){p.push.apply(p,arguments);}; <br /><br />The method is never called within the function.  So I have removed it and the code runs successfully. (A debug function I assume).Have you discovered a need for this? <br />And thanks for adding the error handling portion.<br /><br />Mark
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_804855" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Brian
                           <br />
                           June 22, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="804855" href="#804855">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Just curious, but has anyone managed to find a way to do includes for the templates so that they can be reused on other pages?<br /><br />I tried moving the template out to another file and including it via the src attribute on the script tag but that didn't work out.<br /><br />Thanks in advance.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_809712" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Warren
                           <br />
                           June 27, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="809712" href="#809712">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           I copied and paste the basic example using the John Resig example you modified but I can't seem to render the contents inside the &lt;script type=&quot;text/html&quot; id=&quot;ItemTemplate&quot;&gt;. What am I missing?<br /><br />Even without running the parseTemplate, I can't seem to show any information inside the script tag. e.g &lt;script type=&quot;text/html&quot; id=&quot;ItemTemplate&quot;&gt;Hello World&lt;/script&gt; inside the &lt;body&gt;&lt;/body&gt; but nothing is being displayed.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_809788" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=beb7fdd4bcd15e35472a96ab8182f034&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.west-wind.com/" target="_WebLog" >Rick Strahl</a>
                           <br />
                           June 27, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="809788" href="#809788">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           The template by itself doesn't do anything. It's basically hidden text. You have to parse the template with parseTemplate and the assign the result into the document.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_809982" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Warren
                           <br />
                           June 28, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="809982" href="#809982">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Thanks Rick, it makes sense. It was just as simple as loading it into a document.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_847694" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=f98422ed8ee4c03dd443eb77814fe4c1&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           vic
                           <br />
                           August 04, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="847694" href="#847694">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           hey, this is a great article. i am looking for a templating solution that conforms to POSH html. i dont want any control mechanism in the templates , like if's or loops. The first example seems like it could conform to POSH html very well. the designer can design the html and css, and the programmer can do the js.  i think my only question is with the UpdatePortfolioItem() function in the first example. this would imply what i would need a template update function for each type of template.  do you think its possible that a function could run through the ids and get the corresponding value from the json data?  that way one function could update any template???
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_848386" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=f98422ed8ee4c03dd443eb77814fe4c1&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           vic
                           <br />
                           August 05, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="848386" href="#848386">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           you could use something like this...<br /><br /><br />function updateTemplateDynamically(jItem, jsonData)<br />{<br />                                   jItem.find(&quot;.updateMe&quot;).each(function()<br />                                     {<br />                                             var id = $(this).attr(&quot;id&quot;);<br />                                             jItem.find(&quot;#&quot; + id).text(jsonData[id]);<br />                                     });<br />                             }<br /><br /><br /><br />template would like like:<br /><br /><br /> &lt;div id=&quot;dynamicTestTemplate&quot; class=&quot;itemtemplate&quot; style=&quot;display:none&quot;&gt;<br />                                     &lt;div class=&quot;updateMe&quot; id=&quot;headline&quot;&gt;&lt;/div&gt;<br />                                     &lt;div class=&quot;updateMe&quot; id=&quot;text&quot;&gt;&lt;/div&gt;<br />                                     &lt;div class=&quot;updateMe&quot; id=&quot;OID&quot;&gt;&lt;/div&gt;<br />                             &lt;/div&gt;
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_886920" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://aefxx.com" target="_WebLog" >ae.fxx</a>
                           <br />
                           September 08, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="886920" href="#886920">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Hey guys.<br /><br />You might want to give <a href='http://aefxx.com/jquery-plugins/jqote/'>http://aefxx.com/jquery-plugins/jqote/</a> a try.<br />It's John Resig's micro templating code ported to the jQuery framework.<br /><br />The conversion part has been optimized (tiny gain of speed) and the ability to convert<br />multiple templates at once added.<br /><br />jQote is a jQuery JavaScript templating plugin.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_887771" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=beb7fdd4bcd15e35472a96ab8182f034&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.west-wind.com/" target="_WebLog" >Rick Strahl</a>
                           <br />
                           September 09, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="887771" href="#887771">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           @ae.fxx - One problem with this is that it's using &lt;% %&gt; which will blow up in ASP.NET as the server parsing will process it. The one I have here will work with ASP.NET proper. Might be nice to add the delimiter string as an optional parameter to the jQuery plug-in.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_66331" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=cfcbd314a5a3a54a2db16aeb1f26dc54&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.donatemate.com" target="_WebLog" >Yuval Kaplan</a>
                           <br />
                           November 06, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="66331" href="#66331">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Rick,<br /><br />Firstly, great writing. No run around - we all want to get the job done and you always show us the way.<br /><br />Secondly, I started using jTemplate but encountered a parsing problem that showed up on Firefox but on on IE8. It way be due to an error I have on the page that is not related to the script itself but the fact is it just wouldn't work.<br /><br />I found the alternative by John Resig much more clear and less cryptic. <br /><br />(I used your version as I don't like to confuse with the server tags) <br /><br />Thanks for the great post!
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_132286" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=e8f67d625e74826f0404bbf26f102c5c&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.celerity.nl/tomas/" target="_WebLog" >Tomas Salfischberger</a>
                           <br />
                           December 21, 2009
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="132286" href="#132286">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Great overview, nice to see some updates on the Micro-Templating idea. We use the Micro-Templating concept in our crm tool (Unfortunately only in Dutch: <a href='http://celerity-crm.nl/'>http://celerity-crm.nl/</a>) for separating markup elements from Javascript code and re-use of some of the snippets.<br /><br />To make this a little more maintainable I have combined John's original template idea with some simple configuration options to form a jQuery plugin. (And refactored it to be a little more verbose but more understandable to normal humans ;-))<br /><br />The basic usage of the plugin is to create a template just like John does (or using a textarea if you like) and then update an elements contents like this:<br /><br /><div class='commentcode'><pre class="csharpcode">
$("#someElement").fromTemplate("templateId", dataset)
</pre></div><br /><br />Where dataset is the data you want to expose to the template (usually the results from a JSON(P) call). The code is shared here: <a href='http://github.com/celerity/jQuery-fromTemplate-plugin'>http://github.com/celerity/jQuery-fromTemplate-plugin</a> feel free to improve / comment on it.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_306767" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=5542ef4d8792de94c54a04fb04aae1b3&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://shercorp.net" target="_WebLog" >Sherry Ann</a>
                           <br />
                           March 09, 2010
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="306767" href="#306767">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Hi Sir,<br /><br />How do you format the date and money values inside the template/<br /><br />Thanks,<br />Sherry
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_335233" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=e356fc66b4ff116ce1f2ffee549972d5&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.flashmint.com/show-type-jquery.html" target="_WebLog" >jq</a>
                           <br />
                           March 16, 2010
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="335233" href="#335233">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           returning to this post after a number of months ... Have you tried using &lt;#= &quot;'&quot; #&gt; for the single quote issue?
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_350414" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.ajaxian.com" target="_WebLog" >jhs</a>
                           <br />
                           March 19, 2010
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="350414" href="#350414">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Here is a new jquery template plugin &quot;YATE&quot; that separates the template markup from the javascript data and logic pretty nicely: <a href='http://labs.mudynamics.com/2010/03/19/yate-javascript-templating-engine-for-agile-ui-development/'>http://labs.mudynamics.com/2010/03/19/yate-javascript-templating-engine-for-agile-ui-development/</a>
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_449471" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://diyism.com" target="_WebLog" >diyism</a>
                           <br />
                           April 15, 2010
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="449471" href="#449471">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Give a try to the real &quot;jquery micro template&quot;, it need no seperate template container:<br /><a href='http://plugins.jquery.com/project/micro_template'>http://plugins.jquery.com/project/micro_template</a><br /><br /><div class='commentcode'><pre class="csharpcode">
<span class="kwrd">&lt;</span><span class="html">div</span> <span class="attr">id</span><span class="kwrd">="test1"</span> <span class="attr">class</span><span class="kwrd">="users"</span><span class="kwrd">&gt;</span>
     <span class="kwrd">&lt;!</span><span class="html">for</span> (<span class="attr">var</span> <span class="attr">i</span>=<span class="attr">0</span>;<span class="attr">i</span>&<span class="attr">lt</span>;<span class="attr">users</span>.<span class="attr">length</span>;++<span class="attr">i</span>)
           {!<span class="kwrd">&gt;</span>
<span class="kwrd">&lt;</span><span class="html">div</span> <span class="attr">onMouseOver</span><span class="kwrd">="/*=users[i].color?'this.style.color=\''+users[i].color+'\';':''*/"</span> <span class="attr">id</span>="<span class="attr">user_</span>&<span class="attr">lt</span>;!=<span class="attr">i</span>!<span class="kwrd">&gt;</span>" class='user'<span class="kwrd">&gt;</span>Name:<span class="kwrd">&lt;</span><span class="html">a</span> <span class="attr">href</span>="&<span class="attr">lt</span>;!=<span class="attr">users</span>[<span class="attr">i</span>].<span class="attr">name</span>!<span class="kwrd">&gt;</span>"<span class="kwrd">&gt;&lt;!</span>=users[i].name!<span class="kwrd">&gt;&lt;/</span><span class="html">a</span><span class="kwrd">&gt;&lt;/</span><span class="html">div</span><span class="kwrd">&gt;</span>
           <span class="kwrd">&lt;!</span>}
     !<span class="kwrd">&gt;</span>
<span class="kwrd">&lt;</span><span class="html">pre</span><span class="kwrd">&gt;</span>'somthing'
else ...<span class="kwrd">&lt;/</span><span class="html">pre</span><span class="kwrd">&gt;</span>
<span class="kwrd">&lt;/</span><span class="html">div</span><span class="kwrd">&gt;</span>
&nbsp;
<span class="kwrd">&lt;</span><span class="html">script</span><span class="kwrd">&gt;</span>&nbsp;
<span class="kwrd">var</span> data1={users:[{name:<span class="str">'name1.1'</span>},
                  {name:<span class="str">'name1.2'</span>, color:<span class="str">'yellow'</span>}
                 ]
          };
$(<span class="str">'#test1'</span>).drink(data1);
<span class="kwrd">&lt;/</span><span class="html">script</span><span class="kwrd">&gt;</span>
</pre></div>
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_576395" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://blog.theaccidentalgeek.com/post/2010/05/24/SocialScapes-Twitter-Widget.aspx" target="_WebLog" >The Accidental Geek</a>
                           <br />
                           May 24, 2010
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="576395" href="#576395">#</a>
                           SocialScapes Twitter Widget
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           SocialScapes Twitter Widget
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_726576" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.ewanmce.co.uk/blog" target="_WebLog" >Ewan</a>
                           <br />
                           July 27, 2010
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="726576" href="#726576">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Hi, <br /><br />I'm guessing that this line:<br /><br />    .replace(/'(?=[^#]*#&gt;)/g, &quot;\t&quot;)<br /><br />is supposed to temporarily remove apostrophes which are within &lt;# #&gt; blocks. But it blew up for me when I had a lone # in a code block. So I've replaced it for my own purposes with:<br />      <br />    .replace(/'(?=(?:[^#]|#[^&gt;])*#&gt;)/g, &quot;\t&quot;)<br /><br />which seemed to help.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_782406" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           ismail codar
                           <br />
                           October 01, 2010
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="782406" href="#782406">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Recent simple and powerfull template engine is: <a href='http://github.com/ismail-codar/templatejs'>http://github.com/ismail-codar/templatejs</a>
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_786682" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=16af8c5c73a00fe6d8700bcb0b065da6&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Alexander
                           <br />
                           October 06, 2010
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="786682" href="#786682">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           I really like John's micro templating approach and plan to use it. That it bombs out whenever the - probably not very experienced - template creator accesses a null object or non existing properties of the javascript object that is passed in is not optimal. The template creator should get some kind of feedback what error occured and where, so I changed the regular expression a little bit to cover this:<br /><br />            str.replace(/[\r\t\n]/g, &quot; &quot;)<br />               .replace(/'(?=[^#]*#&gt;)/g, &quot;\t&quot;)<br />               .split(&quot;'&quot;).join(&quot;\\'&quot;)<br />               .split(&quot;\t&quot;).join(&quot;'&quot;)<br />                //.replace(/&lt;#=(.+?)#&gt;/g, &quot;',$1,'&quot;)<br />               .replace(/&lt;#=(.+?)#&gt;/g, &quot;'); try { p.push($1); } catch(err) { p.push('Error: ', err.description); }; p.push('&quot;)<br />               .split(&quot;&lt;#&quot;).join(&quot;');&quot;)<br />               .split(&quot;#&gt;&quot;).join(&quot;p.push('&quot;)<br />               + &quot;');}return p.join('');&quot;;<br /><br />Now if the template writer would include something like &lt;#= foo.bar #&gt; and if foo is null it would show an error instead of bombing out completely.
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_943962" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://linkedin.com/in/medmunds" target="_WebLog" >Mike Edmunds</a>
                           <br />
                           March 01, 2011
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="943962" href="#943962">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           @Mark: Don't know if you're still following this, but...<br /><br />The print() function lets you output content within an embedded &lt;%...%&gt; block.<br /><br />One handy use (that I just ran into) is to call the tmpl() function from within a template -- nested template expansion. Example:<br /><br /><div class='commentcode'><pre class="csharpcode">
<span class="kwrd">&lt;</span><span class="html">script</span> <span class="attr">type</span><span class="kwrd">="text/html"</span> <span class="attr">id</span><span class="kwrd">="post_tmpl"</span><span class="kwrd">&gt;</span>&nbsp;
...
&lt;% $.each(post.comments, <span class="kwrd">function</span>(i, comment){
   print( tmpl(<span class="str">"comment_tmpl"</span>, comment) ); <span class="rem">/* nested call to tmpl() */</span>
}); %&gt;
...
<span class="kwrd">&lt;/</span><span class="html">script</span><span class="kwrd">&gt;</span>
</pre></div><br /><br />You could also code this with &lt;%= instead of print(), but that requires multiple &lt;% blocks, and makes (I think) for uglier code:<br /><br /><div class='commentcode'><pre class="csharpcode">
<span class="kwrd">&lt;</span><span class="html">script</span> <span class="attr">type</span><span class="kwrd">="text/html"</span> <span class="attr">id</span><span class="kwrd">="post_tmpl"</span><span class="kwrd">&gt;</span>&nbsp;
...
&lt;% $.each(post.comments, <span class="kwrd">function</span>(i, comment){ %&gt;
   &lt;%= tmpl(<span class="str">"comment_tmpl"</span>, comment) %&gt;
&lt;% }); %&gt;
...
<span class="kwrd">&lt;/</span><span class="html">script</span><span class="kwrd">&gt;</span>
</pre></div><br /><br />- Mike
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_981487" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Daniel
                           <br />
                           April 01, 2011
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="981487" href="#981487">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           I know this is old, but it's friggin' brilliant!  Many thanks for this post (and a lot of your others).
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_1023826" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           some internet guy
                           <br />
                           May 08, 2011
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="1023826" href="#1023826">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           scripts like these makes JavaScript awesome!  Thanks!<br />The best part is, it works without jQuery! (the title is a little bit confusing ..) <br /><br /><br />if you want to preserve whitespace in your template (ie: you're using &lt; pre &gt; or &lt; textarea &gt;)<br />change<br /><pre><br />	.replace(/[\r\t\n]/g, &quot; &quot;)<br /></pre><br />into<br /><pre><br />	.replace(/\t(?![^#]*#&gt;)/g, &quot;\\t&quot;)	//match tabs outside &lt;##&gt; tags<br />	.replace(/(\r?\n)(?![^#]*#&gt;)/g, &quot;\\n&quot;)	//match newlines outside &lt;##&gt; tags<br />	.replace(/[\r\t\n]/g, &quot; &quot;)		//match whitespace inside &lt;##&gt; tags<br /></pre><br /><br />cheers!
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_1231661" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Darren Hevers
                           <br />
                           January 05, 2012
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="1231661" href="#1231661">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Hey Rick<br /><br />Can you please explain how I can reference multiple templates from a master template.<br /><br />Cheers<br /><br />Darren
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_1231989" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=beb7fdd4bcd15e35472a96ab8182f034&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://www.west-wind.com" target="_WebLog" >Rick Strahl</a>
                           <br />
                           January 05, 2012
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="1231989" href="#1231989">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           @Darren - The micro-template engine is pretty basic so it doesn&#8217;t directly support nested templates.<br /><br />However, expressions and code blocks are JavaScript code so you can simply call ParseTemplate() from within the existing template. It just gen&#8217;s a string so this should work. I haven&#8217;t tried this myself, but this should work:<br /><br /><div class='commentcode'><pre class="csharpcode">
&lt;#= ParseTemplate( $(“#TestTemplate”).html(),document) #&gt;
</pre></div>
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_1275754" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=00&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           Kernel James
                           <br />
                           February 21, 2012
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="1275754" href="#1275754">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           I use the Distal template system <a href='http://code.google.com/p/distal'>http://code.google.com/p/distal</a>
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_1431545" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=d0269f55c7f8fc8e8cc307b453e469a0&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://2basix.nl" target="_WebLog" >leo</a>
                           <br />
                           August 15, 2012
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="1431545" href="#1431545">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           I made a very nice templating engine based upon John's ideas:<br /><br /><a href='http://2basix.nl/page.php?al=javascript-templating-engine'>http://2basix.nl/page.php?al=javascript-templating-engine</a><br /><br />this one is even more lean then the original post from John..<br /><br />Maybe you can check it out..
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_171936" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=7ed47c0a58c545c49e35ac924770010c&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://71104.github.io/jquery-handlebars" target="_WebLog" >71104</a>
                           <br />
                           June 27, 2013
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="171936" href="#171936">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           There is also a Handlebars-based solution: <a href='http://71104.github.io/jquery-handlebars/'>http://71104.github.io/jquery-handlebars/</a><br />
<br />
Disclaimer: I'm the author. :P
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
                
               <div id="cmt_1160429" class="comment">
                   <div class="comment-panel-left">
                       <img src='https://www.gravatar.com/avatar.php?gravatar_id=f5e1fe9f30e18ce5a0f9e2dd37a3100c&size=100&rating=R' style='border-radius: 4px;box-shadow: 2px 2px 4px #5353535; '>
                       <br />
                       <small>
                           <a href="http://codex.wiki" target="_WebLog" >Terry Lin</a>
                           <br />
                           May 17, 2015
                       </small>
                   </div>
                   <div class="comment-panel-right">
                       <h3 style="margin-top: 0; font-size: 1.21em;">
                           <a name="1160429" href="#1160429">#</a>
                           re: Client Templating with jQuery
                       </h3>
                       <div class="commentbody" style="padding-top: 15px;">
                           Your script works good but I found a bug on your script<br />
var new_html = parseTemplate($(&quot;#category-tpl&quot;).html(), {id: res[i].id, text: res[i].link} );<br />
<br />
parseTemplate() can't parse something like res[i].id  ( res.id is fine )
                       </div>

                       
                   </div>
               </div>
               <div class="clearfix"></div>                              
            
    
</div>
    <span id="ctl00_Content_ErrorDisplay" ErrorImage="~/app_themes/"></span>

    <input type="submit" name="ctl00$Content$btnShowCommentTable" value="Add a Comment" id="ctl00_Content_btnShowCommentTable" class="submitbutton" /> 

    
    <div id="ContentOverlay" style="background:steelblue"></div>



    
    

    
    
    
     
    

    

    

    <link href="/scripts/highlightjs/styles/vs2015.css" rel="stylesheet" />
    <script src="/scripts/highlightjs/highlight.pack.js"></script>
    <script>
        setTimeout(function () {
            var pres = document.querySelectorAll("pre>code");
            for (var i = 0; i < pres.length; i++) {
                hljs.highlightBlock(pres[i]);
            }
            var options = {
                contentSelector: "#ArticleBody",
                // Delay in ms used for `setTimeout` before badging is applied
                // Use if you need to time highlighting and badge application
                // since the badges need to be applied afterwards.
                // 0 - direct execution (ie. you handle timing
                loadDelay:0,

                // CSS class(es) used to render the copy icon.
                copyIconClass: "fa fa-copy",
                // CSS class(es) used to render the done icon.
                checkIconClass: "fa fa-check text-success"
            };
            if (window.highlightJsBadge)
                window.highlightJsBadge(options);
        },5);
    </script>
    <script src="/scripts/highlightjs-badge.min.js" ></script>
<br />
</article>
<!--
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
             xmlns:dc="http://purl.org/dc/elements/1.1/"
             xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
<rdf:Description
    rdf:about="https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery"
    dc:identifier="https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery"
    dc:title="Client Templating with jQuery"
    trackback:ping="https://weblog.west-wind.com/posts/2008/Oct/13/Client-Templating-with-jQuery?Trackback=True" />
</rdf:RDF>
-->    


    

    </main>      
</div>


    <footer>
        
        <a href="https://west-wind.com"><img id="ctl00_Image1" align="right" border="0" alt="West Wind" src="../../../../images/wwToolbarlogo.png" /></a>
        &nbsp;<small>&copy; Rick Strahl, West Wind Technologies, 2005 - 2025</small>        
        <p>&nbsp;</p>        
    </footer>





<script type="text/javascript">
//<![CDATA[
var Proxy = { 
    Hello: function (name,completed,errorHandler)
    {
        var _cb = Proxy_GetProxy();
        _cb.callMethod("Hello",[name],completed,errorHandler);
        return _cb;           
    },
    FormatComment: function (inputText,completed,errorHandler)
    {
        var _cb = Proxy_GetProxy();
        _cb.callMethod("FormatComment",[inputText],completed,errorHandler);
        return _cb;           
    },
    DeleteComment: function (id,completed,errorHandler)
    {
        var _cb = Proxy_GetProxy();
        _cb.callMethod("DeleteComment",[id],completed,errorHandler);
        return _cb;           
    },
    ApproveComment: function (id,approve,completed,errorHandler)
    {
        var _cb = Proxy_GetProxy();
        _cb.callMethod("ApproveComment",[id,approve],completed,errorHandler);
        return _cb;           
    },
    DeleteEntry: function (id,completed,errorHandler)
    {
        var _cb = Proxy_GetProxy();
        _cb.callMethod("DeleteEntry",[id],completed,errorHandler);
        return _cb;           
    },
    GetCommentText: function (id,completed,errorHandler)
    {
        var _cb = Proxy_GetProxy();
        _cb.callMethod("GetCommentText",[id],completed,errorHandler);
        return _cb;           
    },
    UpdateCommentText: function (id,html,completed,errorHandler)
    {
        var _cb = Proxy_GetProxy();
        _cb.callMethod("UpdateCommentText",[id,html],completed,errorHandler);
        return _cb;           
    },
    GetRandomBookImage: function (completed,errorHandler)
    {
        var _cb = Proxy_GetProxy();
        _cb.callMethod("GetRandomBookImage",[],completed,errorHandler);
        return _cb;           
    }
}
function Proxy_GetProxy() {
    var _cb = new AjaxMethodCallback('Proxy','/WebLogCallbacks.ashx',
                                    { timeout: 20000,
                                      postbackMode: 'PostMethodParametersOnly',
                                      formName: '' 
                                    });
    return _cb;
}
//]]>
</script>
</form>
        
</div>
</div>




    <script src="/scripts/weblog.js"></script>


    
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-9492219-4"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());

    gtag('config', 'UA-9492219-4');
</script>

</body>
</html>