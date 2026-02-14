Manually search:

```shell
vespa query 'yql=select * from greeting where {"defaultIndex": "message"}userInput(@user-query)' 'user-query=hello' 'hits=3' 'language=en'
```

```shell
vespa query 'yql=select * from greeting where rank({"defaultIndex": "message"}userInput(@user-query), message contains "Eugene")' 'user-query=hello' 'hits=2' 'language=en'
```
