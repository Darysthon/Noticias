# 📰 Portal de Notícias em PHP

Este é um **Portal de Notícias desenvolvido em PHP + MySQL**, com sistema de autenticação de autores, painel administrativo e comentários em notícias.

## 🚀 Funcionalidades

* Cadastro de autores
* Login e autenticação com sessão
* Publicação de notícias
* Edição e exclusão de matérias
* Upload de imagem de capa
* Sistema de comentários nas notícias
* Busca por palavras-chave
* Filtro por categorias

## 🛠 Tecnologias Utilizadas

* PHP
* MySQL
* HTML5
* CSS3
* PDO (PHP Data Objects)

## 📂 Estrutura do Projeto

```
/portal-noticias
│
├── index.php           # Página inicial
├── noticia.php         # Página da notícia
├── login.php           # Login de autores
├── logout.php          # Encerrar sessão
├── cadastrar.php       # Cadastro de autor
├── painel.php          # Painel administrativo
├── editar.php          # Editar notícia
├── excluir.php         # Excluir notícia
├── conexao.php         # Conexão com banco
├── style.css           # Estilos do site
├── uploads/            # Imagens enviadas
└── database.db           # Estrutura do banco de dados
```

## 🗄 Banco de Dados

Crie o banco executando o arquivo SQL:

```
database.db
```

Ou execute manualmente:

```sql
CREATE DATABASE portall_noticias;
```

Depois importe as tabelas.

## ⚙️ Configuração

Edite o arquivo `conexao.php`:

```php
$host = 'localhost';
$dbname = 'portall_noticias';
$usuario = 'root';
$senha = '';
```

## ▶️ Como executar

1. Coloque o projeto dentro do **htdocs** (XAMPP) ou **www** (WAMP)

Exemplo:

```
htdocs/portal-noticias
```

2. Inicie:

* Apache
* MySQL

3. Acesse no navegador:

```
http://localhost/portal-noticias
```

## 🔐 Segurança Implementada

* Senhas criptografadas com `password_hash()`
* Verificação com `password_verify()`
* Uso de **PDO Prepared Statements**
* Proteção contra **SQL Injection**

## 📌 Melhorias Futuras

* Sistema de likes
* Upload de múltiplas imagens
* Editor de texto rico (TinyMCE ou CKEditor)
* Sistema de categorias no banco
* Paginação de notícias

## 👨‍💻 Autor

Projeto desenvolvido para fins de estudo.
