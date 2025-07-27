# 🛒 Sistema de Compra e Venda - Projeto Prático

Este projeto é um sistema simples de compra e venda desenvolvido em **PHP puro com SQLite**. Ele permite o cadastro e login de usuários (clientes e vendedores), além da gestão de produtos, visualização de compras e controle de permissões por tipo de usuário.

---

## ✅ Checklist de funcionalidades implementadas

- [x] Autenticação com controle de sessão
- [x] Cadastro e login de clientes e vendedores
- [x] Permissões separadas por tipo de usuário
- [x] Cadastro, edição, exclusão e listagem de produtos (vendedor)
- [x] Visualização de produtos disponíveis (cliente)
- [x] Registro de compras com desconto e forma de pagamento
- [x] Atualização automática do estoque
- [x] Sistema de reserva temporária de produto (120s)
- [x] Cliente visualiza somente suas compras
- [x] Vendedor visualiza apenas as compras dos seus produtos
- [x] Edição de perfil e senha

---

## 🚀 Como rodar o projeto localmente

### Pré-requisitos

- PHP 7.4 ou superior
- [XAMPP](https://www.apachefriends.org/pt_br/index.html) ou servidor Apache + PHP
- Composer instalado (para autoload)
- Navegador atualizado

### Passo a passo

1. **Clone ou extraia os arquivos do projeto** para dentro da pasta `htdocs` do XAMPP:

```bash
C:\xampp\htdocs\Projeto
```

2. **Instale as dependências via Composer** (somente se necessário):

```bash
composer install
```

3. **Inicie o Apache pelo XAMPP**.

4. **Acesse no navegador:**

```plaintext
http://localhost/Projeto/public/login.php
```

> O banco de dados `db.sqlite` já está incluso e os testes de cliente/produto podem ser feitos via os arquivos `inserir_cliente_teste.php` e `inserir_produto_teste.php`.

---

## 📌 Diagrama de funcionamento (simplificado)

Para abertura no Plant UML
(link: https://www.plantuml.com/plantuml/umla/VPAnJiCm48RtUufJ9nW2Ru0AhGH3Xq2bBeZX6X_H2ISEzcK1yJGCF4XV34Tr1Yk6RBxlp-Nxawo3ajUniw2Ybd4hr7hg2FVI3LPUfI7Zi8h7e1p7Xd7Zki4JCXwuDmH0ZJgF6olSAwVJXDWu3uQKZXGOL_l7dIZZXJbUXNShofShf83REu21cZTgfqnwIlrGh_0JC5xFEzTAVtqxAlSQRKu_fs_tSaRtxBblX3pMtfbHNKZW864aAnzByiJdUMgnoATvZ7y1f4vOLJT_USn5_ybaUyfkMMBXcD6YQwPHV2zvehoah3_TC7sbqjQP5KlDSD5d1YMBcS4w-aw_vIy0)

@startuml
title Diagrama Simplificado - Sistema de Compra e Venda

actor Cliente
actor Vendedor
database "SQLite (db.sqlite)" as DB

package "Sistema PHP" {
    [Login/Autenticação]
    [Gerenciar Produtos]
    [Visualizar Produtos]
    [Realizar Compra]
    [Visualizar Compras]
}

Cliente --> [Login/Autenticação]
Vendedor --> [Login/Autenticação]

Cliente --> [Visualizar Produtos]
Cliente --> [Realizar Compra]
Cliente --> [Visualizar Compras]

Vendedor --> [Gerenciar Produtos]
Vendedor --> [Visualizar Compras]

[Login/Autenticação] --> DB
[Gerenciar Produtos] --> DB
[Visualizar Produtos] --> DB
[Realizar Compra] --> DB
[Visualizar Compras] --> DB
@enduml

## ⚙️ Estrutura de Pastas

```
Projeto/
├── db.sqlite                  # Banco de dados SQLite
├── criar_banco.php           # Script para gerar o schema
├── inserir_cliente_teste.php # Cliente de teste
├── inserir_produto_teste.php # Produto de teste
├── src/                      # Lógica de negócio
│   ├── Models/
│   │   ├── Usuario.php
│   │   ├── Produto.php
│   │   ├── Compra.php
│   │   └── Conexao.php
├── public/                   # Arquivos acessíveis via navegador
│   ├── login.php
│   ├── dashboard_cliente.php
│   ├── dashboard_vendedor.php
│   ├── produtos_disponiveis.php
│   ├── minhas_compras.php
│   └── ...
├── composer.json
├── composer.lock
└── README.md
```

---

## 🧪 Bônus implementados

- [x] **Reserva de produtos** com tempo limite (120 segundos)
- [x] Exportação de PDF via dompdf (biblioteca já configurada)
- [ ] Integração com meios de pagamento
- [x] Relatórios por vendedor
- [ ] Upload de imagem do produto
- [ ] Front-end com JavaScript ou melhorias visuais

---

## ✍️ Autoria

> Projeto desenvolvido por **Amanda Morais Martinelli**  
> [Notion do Projeto](https://www.notion.so/Projeto-pr-tico-2209b6d7d7978037ae16f5b72712307b)

---
