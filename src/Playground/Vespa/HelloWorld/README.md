Deploy the application:

```shell
vespa deploy -t http://localhost:19071 .
```

Very limited search:

```shell
vespa query 'yql=select * from greeting where name contains "Alice" or message contains "Alice"'  'hits=3' 'language=en'
```

Passing user input into just doesn't do any parsing:

```shell
# No results:
vespa query 'yql=select * from greeting where name contains "Alice lessons" or message contains "Alice lessons"'  'hits=3' 'language=en'
```

```shell
bin/console app:vespa:hello:greeting-save 1
bin/console app:vespa:hello:greeting-get 1
bin/console app:vespa:hello:greeting-search "Alice lessons"
```

Manually search:

```shell
vespa query 'yql=select * from greeting where {"defaultIndex": "message"}userInput(@user-query)' 'user-query=hello' 'hits=3' 'language=en'
```

```shell
vespa query 'yql=select * from greeting where rank({"defaultIndex": "message"}userInput(@user-query), message contains "Eugene")' 'user-query=hello' 'hits=2' 'language=en'
```
