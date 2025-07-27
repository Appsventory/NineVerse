@extends('layouts.master')

@section('title')
Beranda
@endsection

@section('content')
<div class="max-w-5xl w-full">
    <header class="flex items-center space-x-3 mb-5">
        <h1 class="text-[#DC143C] font-semibold text-2xl select-none">{{env('APP_NAME')}}</h1>
    </header>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-[#1f293e] rounded-md p-6">
        <section class="border-b border-[#2c3a5a] md:border-b-0 md:border-r md:pr-6 pb-6 md:pb-0">
            <div class="flex items-center space-x-2 mb-2 text-[#94a3b8] text-sm font-semibold">
                <i class="fas fa-book-open"></i>
                <span><a class="text-lg underline hover:text-[#DC143C]" href="#">Documentation</a></span>
            </div>
            <p class="text-sm leading-tight text-[#cbd5e1]">
                With detailed and well-organized documentation, NineVerse makes it easy to understand every aspect of the framework. Whether you're just getting started or already familiar with it, we recommend a full read-through.
            </p>
        </section>
        <section class="border-b border-[#2c3a5a] md:border-b-0 md:pl-6 pb-6 md:pb-0">
            <div class="flex items-center space-x-2 mb-2 text-[#94a3b8] text-sm font-semibold">
                <i class="fas fa-code"></i>
                <span><a class="text-lg underline hover:text-[#DC143C]" href="#">Nixs Template Engine</a></span>
            </div>
            <p class="text-sm leading-tight text-[#cbd5e1]">
                With Nixs, writing PHP code becomes much cleaner and more organized. This template engine is designed to simplify your code structure while improving readability.
            </p>
        </section>
        <section class="border-t border-[#2c3a5a] md:border-t-0 md:border-r md:pr-6 pt-6 md:pt-0">
            <div class="flex items-center space-x-2 mb-2 text-[#94a3b8] text-sm font-semibold">
                <i class="fas fa-terminal"></i>
                <span><a class="text-lg underline hover:text-[#DC143C]" href="#">Fany CLI</a></span>
            </div>
            <p class="text-sm leading-tight text-[#cbd5e1]">
                Fany is a command-line interface (CLI) tool designed to help developers easily execute various essential commands for application management. With Fany, tasks such as code generation, database migrations, controller creation, and other development activities become faster, more organized, and efficient, accelerating the overall application development cycle.
            </p>
        </section>
        <section class="border-t border-[#2c3a5a] md:border-t-0 md:pl-6 pt-6 md:pt-0">
            <div class="flex items-center space-x-2 mb-2 text-[#94a3b8] text-sm font-semibold">
                <i class="fas fa-globe"></i>
                <span><a class="text-lg underline hover:text-[#DC143C]" href="#">Ecosystem</a></span>
            </div>
            <p class="text-sm leading-tight text-[#cbd5e1]"></p>
        </section>
    </div>
    <footer class="flex justify-between items-center mt-6 text-xs text-[#94a3b8]">
        <div class="flex space-x-3">
            <a class="flex items-center space-x-1 hover:text-[#DC143C]" href="https://github.com/Appsventory/NineVerse">
                <i class="fab fa-github"></i>
                <span>Github</span>
            </a>
        </div>
        <div>Build {{env('APP_VERSION')}}</div>
    </footer>
</div>
@endsection