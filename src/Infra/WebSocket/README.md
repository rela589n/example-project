# WebSocket

## Running a prototype js application

1. Get centrifugo token using [get_centrifugo_token.http](_Support/Bundle/Resources/http-client/get_centrifugo_token.http) and put this token
    instead of a `<your_centrifugo_jwt_token>` in [index.html](_Support/Bundle/Resources/prototype/index.html) file.

2. Serve `index.html` file from built-in server:

    ```shell
    docker compose exec centrifugo sh
    centrifugo serve -d ./prototype/ -p 3000
    ```

3. Open your web browser at http://localhost:3003/

4. Dispatch test event using [publish_centrifugo_test_event.http](_Support/Bundle/Resources/http-client/publish_centrifugo_test_event.http)

5. See that the event appears on the page and in the browser console. 
