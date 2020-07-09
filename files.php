<?php
namespace CurrencyConverter;

require_once PLUGIN_FOLDER  . '/src/Storage/Storage.php';
require_once PLUGIN_FOLDER  . '/src/Storage/DBStorage.php';
require_once PLUGIN_FOLDER  . '/src/RemoteService/RemoteService.php';
require_once PLUGIN_FOLDER  . '/src/RemoteService/CoinMarketCap.php';

require_once PLUGIN_FOLDER  . '/src/Currency/Currency.php';
require_once PLUGIN_FOLDER  . '/src/Currency/CMCCurrency.php';
require_once PLUGIN_FOLDER  . '/src/Operation/Operation.php';
require_once PLUGIN_FOLDER  . '/src/Operation/CMCOperation.php';

require_once PLUGIN_FOLDER  . '/src/Renderer/Renderer.php';
require_once PLUGIN_FOLDER  . '/src/Renderer/SimpleView.php';

require_once PLUGIN_FOLDER  . '/src/Converter/Converter.php';
require_once PLUGIN_FOLDER  . '/src/Converter/CMCConverter.php';

require_once PLUGIN_FOLDER  . '/App.php';

require_once PLUGIN_FOLDER  . '/src/AppStates/AppState.php';
require_once PLUGIN_FOLDER  . '/src/AppStates/AdminArea.php';
require_once PLUGIN_FOLDER  . '/src/AppStates/PageArea.php';
require_once PLUGIN_FOLDER  . '/src/AppStates/Ajax.php';
require_once PLUGIN_FOLDER  . '/src/AppStates/CronJobs.php';