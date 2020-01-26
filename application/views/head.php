<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$_title = 'm3 archívum archívum';

?><!DOCTYPE html>
<html>
<head>
	<title><?php echo $_title; ?></title>

	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">

  	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Material+Icons&display=block" rel="stylesheet">

	<link href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css" rel="stylesheet">

	<style type="text/css">
		html {
			margin: 0;
			padding: 0;
		}
		body {
			margin: 0;
			padding: 0 20px 20px 20px;
		}
		h1 {
			text-align: center;
		}
		#search-form {
			text-align: center;
		}
		#search-field {
			width: 66vw;
		}
		.paginator__wrapper {
			margin-top: 20px;
		}
		.paginator__tag_open {
			font-weight: bold;
			cursor: auto !important;
		}
		.cell__title {
			white-space: normal;
  			word-wrap: break-word;
		}
		.cell__title--title {
			font-weight: bold;
		}
		.cell__title--subtitle {
			font-style: italic;
		}
		.cell__title--ep {
			font-style: italic;
		}
		footer {
			color: lightgray;
			text-align: center;
		}
	</style>
</head>
<body class="mdc-typography">

	<header>
		<h1 class="mdc-typography--headline5"><?php echo $_title; ?></h1>
	</header>

	<main class="main-content mdc-typography--body2">
