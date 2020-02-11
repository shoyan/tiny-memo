# tiny_memo
シンプルなメモアプリです。

## インストール

WebサーバーとMySQLを用意してください。
XAMPPやMAMPを利用すると簡単に環境を作成することができます。
このアプリはXAMPPやMAMP環境で動作することを想定して開発されています。

## データベースの準備

### データベースの作成

```
CREATE DATABASE tiny_memo;
```

### テーブルの準備
init.sqlを実行してテーブルを作成してください。

### htdocsに配置

ファイルをhtdocs配下に配置して、次のURLにアクセスしてください。
`http://localhost:8888/tiny_memo/`
