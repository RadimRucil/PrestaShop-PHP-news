<?php
// Zdrojové RSS kanály pro PrestaShop
$prestashop_sources = [
    '1Presta.cz Blog' => 'https://blog.1presta.cz/feed/',
    'PrestaShop Fórum - České fórum' => 'https://www.prestashop.com/forums/forum/83-%C4%8Desk%C3%A9-f%C3%B3rum/?rss=1',
    'PrestaShop Fórum - Podpora a pomoc komunity' => 'https://www.prestashop.com/forums/forum/90-podpora-a-pomoc-komunity/?rss=1',
];

// Zdrojové RSS kanály pro PHP
$php_sources = [
    'PHP.net News' => 'https://www.php.net/news.rss',
    'SitePoint PHP Blog' => 'https://www.sitepoint.com/php/feed/',
    'PHPDeveloper.org' => 'https://www.phpdeveloper.org/feed',
    'WebSupport Blog' => 'https://www.websupport.sk/blog/feed/',
];

$prestashop_data = [];
$php_data = [];

// Funkce pro načtení RSS dat
function fetch_rss_data($sources)
{
    $data = [];
    foreach ($sources as $source_name => $url) {
        $feed = @simplexml_load_file($url);
        if ($feed) {
            foreach ($feed->channel->item as $item) {
                $data[] = [
                    'title' => (string) $item->title,
                    'link' => (string) $item->link,
                    'source' => $source_name,
                    'date' => (string) $item->pubDate,
                ];
            }
        }
    }
    // Seřadit podle data (nejnovější nahoře)
    usort($data, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    return $data;
}

// Načíst data pro PrestaShop a PHP
$prestashop_data = fetch_rss_data($prestashop_sources);
$php_data = fetch_rss_data($php_sources);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trendy v PrestaShop a PHP</title>

    <!-- Odkaz na Font Awesome pro ikony -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #f5f5f5;
            color: #003366;
            padding: 15px 0;
            text-align: center;
            font-size: 1.5em;
            z-index: 1000;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 51, 102, 0.5);
            text-transform: uppercase; /* Text ve headeru bude velkými písmeny */
        }
        .container {
            display: flex;
            width: 90%;
            margin: 120px auto 20px auto;
            gap: 20px;
        }
        .column {
            flex: 1;
            background: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            height: auto;
            box-shadow: 0 4px 10px rgba(0, 51, 102, 0.5);
        }
        .column h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .card {
            margin-bottom: 20px;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .card h3 {
            margin: 0;
            font-size: 1.2em;
        }
        .card p {
            margin: 5px 0;
        }
        .card a {
            color: #000; /* Změněno na černou barvu pro odkazy */
            text-decoration: none;
        }
        .card a:hover {
            text-decoration: underline;
        }

        .source-icon {
            color: #003366;
            margin-right: 5px;
        }

        footer {
            width: 100%;
            background-color: #f5f5f5;
            color: #003366;
            text-align: center;
            padding: 10px 0;
            font-size: 1em;
            box-shadow: 0 -4px 10px rgba(0, 51, 102, 0.5);
        }

        .scroll-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background-color: #f5f5f5;
            color: #003366;
            border-radius: 10px;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 51, 102, 0.5);
            z-index: 999;
            transition: opacity 0.3s ease;
        }

        .scroll-to-top:hover {
            background-color: #e0e0e0;
        }

        .scroll-to-top.visible {
            display: flex;
            opacity: 1;
        }

        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            padding-bottom: 10px;
        }

        .load-more {
            padding: 5px 10px;
            background-color: #f5f5f5;
            color: #003366;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            font-size: 1em;
            box-shadow: 0 4px 10px rgba(0, 51, 102, 0.5);
            transition: background-color 0.3s ease;
            width: auto;
            margin-left: calc(3% + 10px);
            margin-right: calc(3% + 10px);
        }

        .load-more:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
    <header>
        Novinky ve světě PrestaShop a PHP
    </header>

    <div class="container">
        <!-- Levý sloupec: PrestaShop -->
        <div class="column" id="prestashopContainer">
            <h2>PrestaShop</h2>
        </div>

        <!-- Pravý sloupec: PHP -->
        <div class="column" id="phpContainer">
            <h2>PHP</h2>
        </div>
    </div>

    <div class="button-container">
        <button class="load-more" id="loadMoreButton">Načíst další články</button>
    </div>

    <footer>
        &copy; <?= date("Y"); ?> Radim Ručil
    </footer>

    <div class="scroll-to-top" id="scrollToTop">
        ↑
    </div>

    <script>
        // Načíst prvních 7 článků
        const prestashopData = <?= json_encode(array_slice($prestashop_data, 0, 7)); ?>;
        const phpData = <?= json_encode(array_slice($php_data, 0, 7)); ?>;

        // Funkce pro zobrazení článků
        function displayArticles(data, containerId) {
            const container = document.getElementById(containerId);
            data.forEach(item => {
                const article = document.createElement('div');
                article.classList.add('card');
                
                let icon = '';
                if (item.source.includes('Blog')) {
                    icon = '<i class="fa fa-newspaper source-icon"></i>'; // Ikona pro novinky/blog
                } else if (item.source.includes('Fórum')) {
                    icon = '<i class="fa fa-cogs source-icon"></i>'; // Ikona pro fórum
                }
                
                article.innerHTML = `
                    <h3><a href="${item.link}" target="_blank">${item.title}</a></h3>
                    <p>${icon} ${item.source} - ${new Date(item.date).toLocaleDateString()}</p>
                `;
                container.appendChild(article);
            });
        }

        // Zobrazit prvních 7 článků
        displayArticles(prestashopData, 'prestashopContainer');
        displayArticles(phpData, 'phpContainer');

        // Načíst další články
        document.getElementById('loadMoreButton').addEventListener('click', function() {
            const nextPrestashopData = <?= json_encode(array_slice($prestashop_data, 7, 7)); ?>;
            const nextPhpData = <?= json_encode(array_slice($php_data, 7, 7)); ?>;
            displayArticles(nextPrestashopData, 'prestashopContainer');
            displayArticles(nextPhpData, 'phpContainer');
        });

        // Funkce pro šipku nahoru
        const scrollToTopButton = document.getElementById('scrollToTop');
        window.onscroll = function() {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                scrollToTopButton.classList.add('visible');
            } else {
                scrollToTopButton.classList.remove('visible');
            }
        };

        scrollToTopButton.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Automatické posunutí na začátek stránky při jejím načtení
        window.onload = function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
    </script>
</body>
</html>
