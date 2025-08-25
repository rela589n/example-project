Contracts
---------

Buf Registry login:

1. Generate token at https://buf.build/settings/user
2. Place it in `.env.local` (see `.env` example)
3. Login

```shell
source .env.local
echo $BUF_TOKEN | buf registry login --token-stdin
```
