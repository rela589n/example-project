To download the dataset, see [text-search sample app](https://github.com/vespa-engine/sample-apps/tree/master/text-search).

Search by a particular field:
```shell
vespa query 'yql=select * from greeting where {"defaultIndex": "message"}userInput(@user-query)' 'user-query=hello' 'hits=3' 'language=en'
```

Deploy:

```shell
vespa deploy -t http://localhost:19071 .
```

Feed the dataset:
```shell
vespa feed -t http://localhost:8089 dataset/documents.jsonl
vespa feed -t http://localhost:8089 /home/yevhen/Projects/learn/vespa-sample-apps/text-search/dataset/documents.jsonl
```

Configure Vespa to connect:

```shell
vespa config set -l target http://localhost:8089
```

Now, try querying:

```shell
vespa query 'yql=select id, title, url, body from msmarco where userQuery()' \
    'hits=3' 'query=Test-Driven Development' | lnav
```

Try `presentation.summary=minimal`.

Grammar all:
```shell
vespa query 'yql=select * from msmarco where {"grammar":"all"}userInput(@user-query)' \
    'user-query=tdd dcf' 'hits=3' 'language=en' | lnav
```

> Notice that it found only one item (not 100).

Boost those, whose `url` contains `wikipedia.org`:
```shell
vespa query 'yql=select * from msmarco where rank(userInput(@user-query), url contains ("wikipedia.org"))' \
    'user-query=tdd test driven' 'hits=10' 'language=en' | lnav
```

> Notice totalCount: 1654.

Extremely boost `wikipedia.org`:
```shell
vespa query 'yql=select * from msmarco where rank(userInput(@user-query), url contains ({weight:1000, significance:1.0}"wikipedia.org"))' \
    'user-query=tdd test driven' 'hits=10' 'language=en' | lnav
```

Filter by url (without contributing to highlighting and ranking):

```shell
vespa query 'yql=select * from msmarco where userInput(@user-query) and url contains ({filter: true, ranked: false}"wikipedia.org")' \
    'user-query=tdd test driven' 'hits=10' 'language=en' | lnav
```

> Notice totalCount: 548

Debug the query:

```shell
vespa query 'yql=select * from msmarco where userInput(@user-query) and url contains ({filter:true,ranked:false}"medium.com")' \
    'user-query=c++' 'trace.level=3' 'hits=1' 'language=en' 'summary=debug-tokens'
```

Take a look at stemming:

```json
{
    "sddocname": "msmarco",
    "url": "https://medium.com/@byroncrawford/dame-dash-owes-everyone-money-e7add9f4ba04",
    "url-tokens": [
        "http",
        "medium",
        "com",
        "byroncrawford",
        "dame",
        "dash",
        "owe",
        "everyone",
        "money",
        "e7add9f4ba04"
    ]
}
```

Stemmed parts of the url:
- `https` -> `http`;
- `owes` -> `owe`.

Searching by it works since the query uses the same language. Using different would cause problems.

Try a different ranking:

```shell
vespa query 'yql=select id, title, url, body from msmarco where userQuery()' \
    'query=Test-Driven Development' 'hits=3' 'ranking=bm25' | lnav
```

List all available rank features:

```shell
vespa query 'yql=select id, title, rankfeatures from msmarco where userQuery()' \
    'query=Test-Driven Development' 'hits=3' 'ranking=bm25' 'ranking.listFeatures=true' | lnav
```

Use specific rank features:

```shell
vespa query 'yql=select id, title from msmarco where userQuery()' \
    'query=Test-Driven Development' 'hits=3' 'ranking=bm25' 'ranking.profile=collect_rank_features' | lnav
```
