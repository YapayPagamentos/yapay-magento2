# Deprecated

Código descontinuado, favor acessar: https://github.com/vindi/magento2-payments

# Magento Extension for [Yapay Intermediador de Pagamentos](https://www.yapay.com.br/) 

## Instalação

### Versões Compativeis:

- [x] 2.0.x
- [x] 2.1.x
- [x] 2.2.x
- [x] 2.3.x

### Pré requisito:

- Requer a que o PHP esteja no mínimo na versão 7.0.2.

### Instalação do Módulo Yapay:

- Realize o download do módulo e siga os seguintes passos de acordo com a forma que sua loja foi instalada:

#### [Yapay-Magento 2](https://github.com/YapayPagamentos/yapay-magento2.git)


### Instalar usando o Composer
-  Se sua loja foi criada usando o gerenciador de dependência composer, realize os seguintes passos:

1. Crie o diretório ```code``` dentro diretório ```app``` e em seguida crie o diretório ```Yapay```
2. Extraia o conteúdo do download ZIP e mova o diretório ```\Magento2\``` para dentro da pasta ```Yapay```
3. Verifique se a disposição dos diretórios de sua loja está: ```app/code/Yapay/Magento2```
4. Execute o comando ```bin/magento setup:upgrade```
5. Execute o comando ```bin/magento setup:di:compile```
6. Execute o comando ```bin/magento setup:static-content:deploy -f```


### Instalar usando o github
- Caso sua loja tenha sido criada por meio do clone ou download do projeto magento, siga os seguintes passos:

1. Extraia o conteúdo do download ZIP e mova o diretório ```\Magento2\``` para dentro da pasta ```Yapay```
2. Verifique se está dessa maneira seus diretórios na sua loja ```app/code/Yapay/Magento2```
3. Execute o comando ```bin/magento setup:upgrade```
4. Execute o comando ```bin/magento setup:di:compile```
5. Execute o comando ```bin/magento setup:static-content:deploy -f```


### Configuração

Acesse no Painel Administrativo do Magento no menu lateral clique em `Store`, depois clique em `Configuration`, na sequencia clique em `Customer`, depois `Customer Configurarion`, depois acesse a opção `Name and Address Options`. Em `Number of Lines in a Street Address` você deve informar a o número 4, conforme imagem abaixo:

![FOTO 6](img/yapay_dashboard_customer_magento.png)



Após realizar a configuração do Cliente, acesse no Painel Administrativo do Magento No menu lateral clique em `Store`, na sequencia clique em `Configuration`, no sub-menu `Sales` clique em `Payment Methods`. Será carregada a tela para configurar os meios de pagamentos do site. 

![FOTO 1](img/yapay_dashboard_magento.png)


### Como habilitar a Yapay no seu site

No primeiro bloco de informação, estão as informações de configuração da sua conta Yapay. 

- Ambiente
	- [x] Seleciona qual versão de ambiente da Yapay que o site estará apontando. Os ambientes disponíveis são: ```Sandbox``` e ```Produção```.
	
- Token
	- [x] Chave de integração da conta Yapay. Os tokens de produção e sandbox são distintos.
	- [x] O token da conta é gerado após cadastro da conta

- E-mail
	- [x] E-mail utilizado para criação da conta Yapay. 	

	
![FOTO 2](img/yapay_config.png)

### Configurando os meios de pagamentos

#### Cartão de Crédito

- Ativo

- Métodos de pagamento disponíveis

- Número máximo de parcelas

![FOTO 3](img/yapay_ccred.png)

#### Transferência Online

- Ativo

- Métodos de pagamento disponíveis

![FOTO 4](img/yapay_tef.png)

#### Boleto Bancário

- Ativo

- Métodos de pagamento disponíveis

![FOTO 5](img/yapay_bol.png)


