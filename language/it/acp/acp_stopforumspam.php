<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) 2021 Marco (marcomg)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters for use
// ’ » “ ” …


$lang = array_merge($lang, [

	// ACP entries
	'SFS_SETTINGS'			=> 'Impostazioni',
	'SFS_ENABLED'			=> 'Abilita Stop Forum Spam',
	'SFS_ENABLED_EXPLAIN'	=> 'Abilita o disabilita l’estensione. Si applica sia alla registrazione utenti che ai post degli ospiti.',
	'SFS_THRESHOLD_SCORE'	=> 'Soglia Stop Forum Spam',
	'SFS_THRESHOLD_SCORE_EXPLAIN'	=> 'L’estensione verificherà gli utenti basandosi su una soglia (ad esempio, il numero di volte in cui un nome utente, un’e-mail o un indirizzo IP viene trovato nel database di SFS). Puoi inserire qualsiasi numero compreso tra 1 e 99. Più basso è il numero, maggiore sarà la possibilità di un falso positivo. ',
	'SFS_LOG_MESSAGE'		=> 'Logga i messaggi',
	'SFS_LOG_MESSAGE_EXPLAIN'	=> 'Se impostato a si, i messaggi saranno loggati nell’APC (nel registro admin o utente in base all’azione svolta).',
	'SFS_BAN_IP'			=> 'Ban Utente',
	'SFS_BAN_IP_EXPLAIN'	=> 'Se impostato a si, l’IP dell’utente o l’username verranno bannati secondo le indicazioni di “Durata del ban”',
	'SFS_BAN_REASON'		=> 'Mostra il motivo se bannato',
	'SFS_BAN_REASON_EXPLAIN'	=> 'Se “Ban Utente” è impostato a si, puoi scegliere se mostrare oppure no un messaggio all’utente bannato.',
	'SFS_DOWN'				=> 'Permetti se Stop Forum Spam è irraggiungibile',
	'SFS_DOWN_EXPLAIN'		=> 'La registrazione/posting deve essere permessa se Stop Forum Spam è irraggiungibile è raggiungibile?',
	'SFS_API_KEY'			=> 'Chiave per le API di Stop Forum Spam',
	'SFS_API_KEY_EXPLAIN'	=> 'Se vuoi segnalare gli spammer al database di Stop Forum Spam database, inserisci le chiavi API di <a target="_blank" href="http://www.stopforumspam.com/keys" rel="noreferrer noopener">stop forum spam</a> here. Devi essere registrato sul sito di SFS per ottenere la chiave per le API',
	'SFS_NEED_CACHE'		=> 'È stata configurata una chiave API key, ma non è stata generata la cache per gli amministratori e i moderatori. Per favore, clicca il pulsante qui sotto per generare la cache per gli amministratori e i moderatori, in caso contrario potranno essere segnalati.',
	'SFS_NOTIFY'			=> 'Notifiche forum',
	'SFS_NOTIFY_EXPLAIN'	=> 'Se impostata su si e c’è una chiave API configurata, le notifiche forum saranno attivate quando un post viene segnalato a stop forum spam',
	'SFS_CLEAR'				=> 'Cancella i post segnalati',
	'SFS_CLEAR_EXPLAIN'		=> 'Verranno cancellati tutti i post ( %1s in totale ) e i messaggi privati ( %2s in totale ) segnalati a stop forum spam',
	'SFS_CLEAR_SURE'		=> 'Cancella le segnalazioni a SFS',
	'SFS_CLEAR_SURE_CONFIRM'	=> 'Sei sicuro di voler cancellare tutti i post e i messaggi privati segnalati?',
	'SFS_BUILD' => 'Genera la cache per gli amministratori e i moderatori',
	'SFS_BUILD_EXPLAIN'	=> 'Produci la cache per gli admin e i moderatori globali da usare per le segnalazioni a SFS',
	'SFS_NEEDS_API'	=> 'Per produrre la cache ti serve una chiave da stop forum spam',
	// ACP messages
	'SFS_BY_NAME'	=> 'Verifica gli user name',
	'SFS_BY_EMAIL'	=> 'Verifica le email',
	'SFS_BY_IP'		=> 'Verifica gli IP',
	'TOO_SMALL_SFS_THRESHOLD'	=> 'La soglia impostata è troppo bassa.',
	'TOO_LARGE_SFS_THRESHOLD'	=> 'La soglia impostata è troppo alta.',
	'SFS_SETTINGS_ERROR'		=> 'C’è stato un errore durante il salvataggio delle impostazioni. Per favore invia il back trace il report degli errori.',
	'SFS_SETTINGS_SUCCESS'		=> 'Le impostazioni sono state salvate correttamente.',
	'SFS_REPORTED_CLEARED' => 'I post e i messaggi privati segnalati a stop forum spam sono stati resettati.',
	//Donation
	'PAYPAL_IMAGE_URL'          => 'https://www.paypalobjects.com/webstatic/en_US/i/btn/png/silver-pill-paypal-26px.png',
	'PAYPAL_ALT'                => 'Dona su PayPal',
	'BUY_ME_A_BEER_URL'         => 'https://paypal.me/RMcGirr83',
	'BUY_ME_A_BEER'				=> 'Offrimi un caffè per aver creato l’estensione',
	'BUY_ME_A_BEER_SHORT'		=> 'Fai una donazione per l’estensione',
	'BUY_ME_A_BEER_EXPLAIN'		=> 'Questa estensione è completamente gratuita. Su questo progetto ho dedicato il mio tempo ed è disponibile gratuitamente a tutta la comunità di phpBB. Se questa estensione di è stata gradita, o se è stata utile al tuo forum, per favore, tieni in considerazione di <a href="https://paypal.me/RMcGirr83" target="_blank" rel="noreferrer noopener">offrirmi un caffè</a>. Sarà immensamente apprezzato. <i class="fa fa-smile-o" style="color:green;font-size:1.5em;" aria-hidden="true"></i>',
]);
