<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class OauthTest extends TestCase
{

    public function testCall()
    {
        $url = $this->baseUrl."authorize/callback?signature=bafa56e41003b5ebd761328fda33676c5e126ab9&timestamp=1542185831&nonce=133708821&encrypt_type=aes&msg_signature=3287974390047b5209d6924fd78c245f9635f718";
        $content = '<xml>     <AppId><![CDATA[wx598f43e63adfee39]]></AppId>     <Encrypt><![CDATA[zbmsG2uFBi1ytfzsALA7C4+S7aBe3mxBkOX0Sy80Yu9TZz21bsFzXSNYIVyHOiesFdzrEx9sXcip5kqYaH1aYEM4/UdZyuHNPeThI5AVPU4kuYiqG7zmZIjzTko6BpHRdFGrCHdRpdLNNHMnF56NXt+3ahb0DDqYiNOxa3GtYOW2K27EJ1lA7bN0Chta9xDW9MHnRhWTOC/woJnYlyQDvCR2MQOvwTZ45xHc+XkuGck3XB85dXONNSkM4+0Ap5MG1bYl5BQM9R6HfwFbeGBLiHdbafVtsBiKdVtsbfSuuul5+uT7xMIDzXzccM+MvnwW/2Lk7MhBa3beqdfviy1OCx5bs4Q9+kZKAdVPZKEObr8N/jYitGanCkHA5BK0zH30WDZ4IwJOBHSY6vm7gVhh5br16TJu5Xb7pd3rqmqpj8KLLy5L5lx3GRbwMP2z9o265eDziTQzs0/S+oDS8xboEQ==]]></Encrypt> </xml>';
        echo $this->requestByCurl($url, $content);
    }


}
