@extends('layouts.master')

@section('title', 'O našem klubu')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">O našem klubu</h1>

                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Naše historie</h2>
                    <div class="space-y-8">
                        <div class="flex items-start space-x-4">
                            <div class="bg-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <span class="font-bold">1995</span>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 flex-1">
                                <h3 class="font-semibold text-lg text-gray-800 mb-2">Založení klubu</h3>
                                <p class="text-gray-600">
                                    Sportovní klub byl založen skupinou nadšenců s vizí vytvořit komunitní sportovní centrum
                                    pro všechny generace. Začali jsme s jedním fotbalovým týmem a tenisovým oddílem.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-green-600 text-white rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <span class="font-bold">2002</span>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 flex-1">
                                <h3 class="font-semibold text-lg text-gray-800 mb-2">Rozšíření o basketbal</h3>
                                <p class="text-gray-600">
                                    Po úspěších v místních ligách jsme rozšířili nabídku o basketbal.
                                    Postavena byla první sportovní hala a založeny týmy mužů a žen.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-pink-500 text-white rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <span class="font-bold">2010</span>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 flex-1">
                                <h3 class="font-semibold text-lg text-gray-800 mb-2">Modernizace areálu</h3>
                                <p class="text-gray-600">
                                    Kompletní rekonstrukce sportovního areálu s novými šatnami,
                                    klubovnou a zázemím pro sportovce.
                                </p>
                            </div>
                        </div>


                        <div class="flex items-start space-x-4">
                            <div class="bg-red-600 text-white rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <span class="font-bold">2020</span>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 flex-1">
                                <h3 class="font-semibold text-lg text-gray-800 mb-2">Digitální transformace</h3>
                                <p class="text-gray-600">
                                    Zavedení moderních technologií do chodu klubu. Online rezervační systém,
                                    členská aplikace a webové stránky pro lepší komunikaci s členy.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Současnost</h2>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <p class="text-gray-700 mb-4">
                            Dnes jsme moderním sportovním klubem s více než 500 členy a kompletním zázemím
                            pro fotbal, tenis a basketbal. Naším posláním je podporovat sportovní aktivity
                            všech věkových kategorií a vytvářet přátelskou komunitu nadšenců.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">500+</div>
                                <div class="text-gray-600 text-sm">členů klubu</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">3</div>
                                <div class="text-gray-600 text-sm">sportovní oddíly</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">25+</div>
                                <div class="text-gray-600 text-sm">let tradice</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="text-3xl mb-4">🎯</div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Naše poslání</h3>
                        <p class="text-gray-600">
                            Poskytovat kvalitní sportovní vyžití pro všechny věkové kategorie,
                            podporovat fair play a budovat silnou sportovní komunitu v regionu.
                        </p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="text-3xl mb-4">🌟</div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Naše vize</h3>
                        <p class="text-gray-600">
                            Stát se předním sportovním klubem v kraji, který vychovává nové talenty
                            a poskytuje špičkové zázemí pro rekreační i výkonnostní sportovce.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
