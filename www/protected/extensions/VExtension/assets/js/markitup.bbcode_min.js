myBbcodeSettings = {
  nameSpace:          "bbcode", // Useful to prevent multi-instances CSS conflict
  previewParserPath:  "~/sets/bbcode/preview.php",
  markupSet: [
      {name:'Жирный', key:'B', openWith:'[b]', closeWith:'[/b]'},
      {name:'Наклонный', key:'I', openWith:'[i]', closeWith:'[/i]'},
      {name:'С подчеркиванием', key:'U', openWith:'[u]', closeWith:'[/u]'},
      {separator:'---------------' },
      {name:'Ссылка', key:'L', openWith:'[url=[![Ссылка]!]]', closeWith:'[/url]', placeHolder:'Ссылка'},
   ]
}