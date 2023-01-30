# loading-page-php
Página de carregamento do servidor do Garry's Mod TTT

# Primeiro de tudo, vale ressaltar que esta página está sendo descontinuada a partir da data de seu publicamento neste repositório. 
Ou seja, não serão enviadas correções ou adições a ela. Caso desejar agregar ou corrigir algo, faça você mesmo. Seja em um fork e mantendo uma versão própria sua, ou enviando um PR, que será muito bem-vindo. Porém, não nos cobre em relação a criarmos algo específico, pois já estamos trabalhando em uma nova versão, feita do zero, utilizando outras tecnologias back end e front end, já que esta última é o maior defeito desta versão.

# Esta página de carregamento possui alguns requisitos:
* um servidor para hospedagem de conteúdo web (dinâmico) - GitHub pages e outros serviços gratuitos de hospedagem não suportam esta página, pois são feitos para rodar páginas estáticas (ou seja, sem suporte para rodar funcionalidades de back end).
* recomenda-se utilizar a versão mais recente do PHP
* [xPaw PHP Source Query](https://github.com/xPaw/PHP-Source-Query) (e seus respectivos requisitos)
* uma chave da Web API do Steam; [Obtenha a sua aqui](https://steamcommunity.com/dev/apikey)

# Para utilizar esta página de carregamento, é simples:
* crie uma cópia do arquivo data_example.json e renomeie-o para data.json
* nesse arquivo, faça as seguintes alterações:
  ```
    -- preencha o valor abaixo com a sua chave da Web API do Steam
    "0": {
      "api_key": ""
    },
    "query_config": {
      -- preencha abaixo com o endereço principal do IP do seu servidor como uma string (entre ""), sem incluir a porta
      "server_address": "123.456.789.123",
      -- insira o valor da porta do seu servidor na chave abaixo, em forma numérica, seguido de uma "," (27015) 
      "server_port": 27015
    },
  ```
 * quanto às seções de membros da Staff, destaques da temporada e regras do servidor, recomenda-se que respeite as quantidades utilizadas no exemplo. Ou seja, altere como quiser, mas não utilze mais itens do que o exemplo utiliza, pois poderá quebrar a página e poderá fazer com que a exibição fique comprometida em certas resoluções. Como citado acima, esta página não é totalmente otimizada, e foi descontinuada, então é natural que possua limitações.

# Abaixo, veja demonstrações das páginas de carregamento:

Primeiro exemplo - sem os "destaques de temporada", somente uma descrição breve (pode ser do servidor ou do modo de jogo)
![](/examples/1.png)

Segundo exemplo - com os "destaques de temporada" (pode ser algo relacionado a um ranking de jogadores ou qualquer outro destaque que quiser fazer)
![](/examples/2.png)
