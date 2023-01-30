<!DOCTYPE html>
<html lang="pt-br">
    <?php
        require './source_query/bootstrap.php';
        use xPaw\SourceQuery\SourceQuery;

        $config_file = file_get_contents('./data.json');
        $config = json_decode($config_file, true);
        define( 'API_KEY', $config["0"]["api_key"] );
        define( 'SQ_SERVER_ADDR', $config["query_config"]["server_address"] );
        define( 'SQ_SERVER_PORT', $config["query_config"]["server_port"] );
        define( 'SQ_TIMEOUT', 1 );
        define( 'SQ_ENGINE', SourceQuery::SOURCE );

        $Timer = microtime( true );
        $Query = new SourceQuery( );
        $Info = [];
        $Rules = [];
        $players = [];
        $exception = null;
        $server = 'Indefinido';
        $map = 'Indefinido';
        $maxplayers = 'Indefinido';
        try {
            $Query->Connect( SQ_SERVER_ADDR, SQ_SERVER_PORT, SQ_TIMEOUT, SQ_ENGINE );
            $Query->SetUseOldGetChallengeMethod( true ); // Use this when players/rules retrieval fails on games like Starbound
            $Info = $Query->GetInfo();
            $Rules = $Query->GetRules();
            $players = $Info['Players'];
            $server = $Info['HostName'];
            $map = $Info['Map'];
            $maxplayers = $Info['MaxPlayers'];
        }
        catch( Exception $e ) {
            $Exception = $e;
        }
        finally {
            $Query->Disconnect( );
        }
        $Timer = number_format( microtime( true ) - $Timer, 4, '.', '' );

        // receber SteamID64 do jogador que está se conectando ao servidor no momento que a página é carregada
        $id = isset($_GET["steamid"])?$id=$_GET["steamid"]:$id=0;

        /* requisitar informações do jogador que está se conectando de antemão
        sim, sei que não é uma ótima ideia. O correto seria fazer somente uma única requisição
        à API com todas as informações necessárias, mas fica pro próximo episódio */
        $api_url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".API_KEY."&steamids=$id";
        $json = json_decode(file_get_contents($api_url), true);
        if ($json["response"]["players"] == []) {
            $plyname = "Desconhecido";
            $plypic = "images/logo.png";
            $plyid = "SteamID indefinido";
        } else {
            $plyname = $json["response"]["players"][0]["personaname"];
            $plypic = $json["response"]["players"][0]["avatarmedium"];
            $plyid = getSteamId32($id);
        }

        function parseInt($string) {
            // return intval($string);
            if (preg_match('/(\d+)/', $string, $array)) {
                return $array[1];
            } else {
                return 0;
            }
        }

        function getSteamId32($id) {
            // Convert SteamID64 into SteamID
            $subid = substr($id, 4); // because calculators are fags
            $steamY = parseInt($subid);
            $steamY = $steamY - 1197960265728; //76561197960265728
            if ($steamY%2 == 1){
                $steamX = 1;
            } else {
                $steamX = 0;
            }
            $steamY = (($steamY - $steamX) / 2);
            $steamID = "STEAM_0:" . (string)$steamX . ":" . (string)$steamY;
            return $steamID;
        }
        $steamids = $config["users"]["staff"]["items"];
        $str_steamids = '';
        foreach ($steamids as $steamid) {
            $str_steamids .= $steamid.',';
        }

        /* realizar requisição das informações dos perfis públicos dos jogadores necessários à API do Steam */
        @ $url = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".API_KEY."&steamids=".$str_steamids);
            $content = json_decode($url, true);
            $steamdata = [];
            $c = count($steamids);
            for($i = 0; $i < $c; $i++) {
                /* Criar matriz de informações dos perfis Steam requisitados */
                $steamdata[$content['response']['players'][$i]['steamid']] = $content['response']['players'][$i];
            }
        ?>
    <head>

        <!-- Título da página: nome do jogador e informações básicas do servidor (somente visível no navegador) --> 
        <title><?php echo "$plyname - $map ($players/$maxplayers)"; ?></title>
        <meta charset="UTF-8">
        <meta name="author" content="https://github.com/r47orr">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, user-scalable=no" />

        <!-- Exibir foto de perfil do jogador que estiver entrando no servidor como ícone da página --> 
        <?php echo "<link rel='icon' href='$plypic'>\n"; ?>
        <link rel="stylesheet" type="text/css" href="css/mainstyles.css">
        
        <style>
            body {
                
                <?php
                    // Usar como fundo da página o mapa atualmente em execução pelo servidor
                    if (file_exists("./images/maps/".$map.".jpg")) {
                        $mapimg = "images/maps/".$map.".jpg";
                    } else {
                        $mapimg = "images/background.png";
                    };
                    echo "background-image: url(".$mapimg.")";
                ?>;
        }
        </style>
    </head>
        <body>

        <!-- Funcionalidade da parte inferior da página (status de carregamento) --> 
        <script>
            document.getElementById("percentagetxt").innerHTML = "10%";
            document.getelementById("description").innerHTML = "Carregando...";
            function SetStatusChanged( status ) {
                var statusTxt = '';
                if(status == 'Retrieving server info...' || status == 'Obtendo informações do servidor...') {
                    document.getElementById("percentage").style.width = "30%";
                    document.getElementById("percentagetxt").innerHTML = "30%";
                    statusTxt = 'Obtendo informações do servidor...';
                } else if(status == 'Mounting Addons...' || status == 'Montando addons...') {
                    document.getElementById("percentage").style.width = "55%";
                    document.getElementById("percentagetxt").innerHTML = "55%";
                    statusTxt = 'Montando conteúdo da oficina';
                } else if(status == 'Workshop Complete!' || status == 'Workshop concluído!') {
                    document.getElementById("percentage").style.width = "75%";
                    document.getElementById("percentagetxt").innerHTML = "75%";
                    statusTxt = 'Oficina completa!';
                } else if(status == 'Sending client info...') {
                    document.getElementById("percentage").style.width = "80%";
                    document.getElementById("percentagetxt").innerHTML = "80%";
                    statusTxt = 'Enviando informações do cliente...';
                } else if(status == 'Starting Lua...') {
                    document.getElementById("percentage").style.width = "89%";
                    document.getElementById("percentagetxt").innerHTML = "89%";
                    statusTxt = 'Inicializando Lua...';
                } else if(status == 'Lua Started!') {
                    document.getElementById("percentage").style.width = "99%";
                    document.getElementById("percentagetxt").innerHTML = "99%";
                    statusTxt = 'Lua inicializado!';
                } else {
                    document.getElementById("percentage").style.width = "40%";
                    document.getElementById("percentagetxt").innerHTML = "40%";
                }
                document.getElementById("description").innerHTML = statusTxt;
            }
        </script>

        <!-- Seção das informações do jogador que está entrando e do servidor em si --> 
        <div class="playerinfo">
            <div class="content">
                <div class="playersummaries">
                Bem-vindo(a),
                <?php
                    echo $plyname;
                ?>
                <br/>
                <img class="plyavatar" src="<?php echo $plypic; ?>"><br/>
                <?php
                    echo $plyid;
                ?>
                <br/>
                <span style="font-size: 10px;">Se esta página estiver com erros, utilize a BETA <i>x86-64 - Chromium + 64-bit binaries</i> do Garry's Mod</span>
                </div>
                <h3>Guia básico TTT:</h3>
                <p class="ttt">
                O modo de jogo consiste de uma brincadeira de Detetive e Traidor (infiltrado) - como no famoso jogo Among Us.<br/>
                Basicamente, os Inocentes devem descobrir, em conjunto, quem são os Traidores e sobreviver!</br>
                Os Traidores devem matar todos os Inocentes e Detetives!</br>
                Divirta-se!
                </p>
                <ul>
                    <li><h2>BACKSTABBER BRASIL TTT</h2></li>
                    <li>Mapa: <?php echo $map; ?></li>
                    <li>Jogadores: <?php echo "$players/$maxplayers"; ?></li>
                    <li>Modo de jogo: Trouble in Terrorist Town</li>
                    <br>
                </ul>
            </div>
        </div>

        <!-- Seção de amostra dos principais membros da Staff (não há espaço ATUALMENTE para alocar todos) --> 
        <div class="staffinfo">
            <div class="content">
                <h3><?php echo $config["users"]["staff"]["title"]; ?></h3>
                <ul class="staffmembers">
                <?php
                    $c = count($steamids);
                    for ($i = 0; $i < $c; $i++) {
                        $name = $steamdata[$steamids[$i]]['personaname'];
                        $img = $steamdata[$steamids[$i]]['avatar'];
                        echo "<li>\n";
                        echo '<img src="'.$img.'"></img>';
                        echo "$name";
                        echo "</li>\n";
                    }
                ?>
                </ul>
            </div>
        </div>

        <!-- Seção das regras do servidor  --> 
        <div class="rules">
            <div class="content">
                <h3><?php echo $config["rules"]["title"]; ?></h3>
                <ol type="I">
                    <?php
                        $rules = $config["rules"]["items"];
                        $cr = count($rules);
                        for ($i = 0; $i < $cr; $i++) {
                            if ($i == $cr - 1) {
                                echo "</br>";
                                echo "<li>$rules[$i]</li>";
                                break;
                            }
                            echo "<li>$rules[$i]</li>";
                        }
                    ?>
                </ol>
            </div>
        </div>

        <div id="loadingbar">
            <h3 id="description">Carregando...</h3>
            <h3 id="percentagetxt">10%</h3>
            <div id="percentage" style="width: 10%;"></div>
        </div>

    </body>
</html>