@extends('layouts.app')

@section('body')
    <section id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h3 class="text-center">Цены на сырье</h3>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <form>
                        <div class="form-group"><input class="form-control" type="text" placeholder="Фильтр..." v-model="filter" @keyup.esc="filter = ''"></div>
                    </form>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <div class="table-responsive" style="font-size: 12;">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Наименование</th>
                                    <th class="text-center">Ед.изм.</th>
                                    <th class="text-center">Цена за ед.изм., руб.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in itemssorted" v-if="!filter.length || item.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0" style="cursor: pointer" @click.prevent="openitem(item)">
                                    <td>@{{ item.name }}</td>
                                    <td class="text-center">@{{ item.name == 'Электроэнергия' ? '1 кВт/час' : item.edizm }}</td>
                                    <td class="text-center">@{{ rubkop(item.price) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <button class="btn btn-primary" type="button" @click.prevent="openmodal">Добавить</button>
                </div>
            </div>
        </div>
        <div class="modal" role="dialog" tabindex="-1" id="changePrice">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">@{{ currentItem.id ? 'Изменить' : 'Добавить' }} сырье</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group"><label>Наименование</label>
                                <input class="form-control" type="text" v-model="currentItem.name" :disabled="currentItem.name == 'Электроэнергия'">
                            </div>
                            <!--<div class="form-group"><label>Единица измерения</label><input class="form-control" type="text" v-model="currentItem.edizm"></div>-->
                            <div class="form-group mb-0">
                                <label>Цена за @{{ currentItem.name == 'Электроэнергия' ? '1 кВт/час' : '1кг' }}</label>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="col col-3">
                                  <input type="text" class="form-control" style="text-align:right" v-model="currentItem.price.rub" id="rub" @focus="focusinput" @input="onlydigits" inputmode="numeric">
                                </div>
                                <div class="col col-auto">
                                    <span>руб.</span>
                                </div>
                                <div class="col col-2">
                                  <input type="text" class="form-control" style="text-align:center" v-model="currentItem.price.kop" id="kop" @input="checkKop" @focus="focusinput" inputmode="numeric">
                                </div>
                                <div class="col col-auto">
                                    <span>коп.</span>
                                </div>
                          </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="row w-100 ml-0 mr-0">
                            <div class="col col-12 col-md-auto pl-0 pr-0 mb-3 order-12 order-md-1" v-if="currentItem.id && currentItem.name != 'Электроэнергия'">
                                <div class="w-100 d-block d-md-none mb-3"></div>
                                <button class="btn btn-danger btn-block" type="button" data-dismiss="modal" :disabled="disabled" @click="delitem(currentItem.id)">Удалить</button>
                            </div>
                            <div class="col d-none d-md-block order-md-2"></div>
                            <div class="col col-12 col-md-auto pl-0 pr-0 pr-md-2 mb-3 order-6">
                                <button class="btn btn-light btn-block" type="button" data-dismiss="modal">Отмена</button>
                            </div>
                            <div class="col col-12 col-md-auto pl-0 pr-0 mb-3 order-1 order-md-12">
                                <button class="btn btn-primary btn-block" type="button" @click="addEdit(currentItem)" :disabled="disabled">@{{ currentItem.id ? 'Сохранить' : 'Добавить' }}</button>    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="js/prices.js"></script>
@endsection
