@extends('layouts.app')

@section('styles')
    <style type="text/css">
        .removeItem {
            display: none;
            position: absolute;
            top: 20%;
            right: 10%;
            color: red;
            font-weight: bold;
            cursor: pointer;
            font-size: 18px;
        }
        .removeTr:hover .removeItem {
            display: block;
        }
        .marja>input {
            text-align:center;
            width: 50px;
            padding: 0;
            height: auto;
            line-height: 1;
            display: inline-block;
            font-weight: bold;
        }
        .marja::after {
            content: '%';
            position: absolute;
        }
        #useDefMarja {
            position: relative;
            vertical-align: middle;
            margin-top: 0;
        }
        .newDefMarja {
            position: relative;
        }
        .newDefMarja::after {
            content: '%';
            position: absolute;
            display: contents;
        }
    </style>
@endsection

@section('body')
    <section id="app">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h3 class="text-center">Изделия</h3>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <form>
                        <div class="form-row">
                            <div class="col">
                                <input class="form-control" type="text" placeholder="Фильтр..." v-model="filter" @keyup.esc="filter = ''">
                            </div>
                            <div class="col">
                                <label for="" class="col-form-label">Общая наценка</label>
                                <span class="newDefMarja">
                                    <input type="text" style="width: 50px;" v-model="newDefMarja" class="text-center" @focus="focusinput" inputmode="numeric" @input="onlypercentnewdefmarja($event)">
                                </span>
                                <button @click.prevent="saveNewDefMarja" :disabled="disabled" class="btn btn-secondary btn-sm">Сохр</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <div class="table-responsive" style="font-size: 12;">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="w-50">Наименование</th>
                                    <th class="text-center">Затраты на ед.</th>
                                    <th class="text-center">Наценка</th>
                                    <th class="text-center">Отпускная цена</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="p in productssorted" v-if="!filter.length || p.name.toLowerCase().indexOf(filter.toLowerCase()) >= 0" style="cursor: pointer" @click.prevent="openproduct(p)">
                                    <td>@{{ p.name }}</td>
                                    <td class="text-center">@{{ p.costPerOne }}</td>
                                    <td class="text-center">@{{ p.marja + '%' }}</td>
                                    <td class="text-center">@{{ p.price }}</td>
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
                        <h4 class="modal-title">@{{ currentProduct.id ? 'Изменить' : 'Добавить' }} изделие</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button></div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group"><label>Наименование</label><input class="form-control" type="text" v-model="currentProduct.name"></div>
                            <div class="form-group">
                                <div class="form-row align-items-center">
                                    <div class="col col-auto">
                                        <span>Количество в одной партии, шт.</span>
                                    </div>
                                    <div class="col">
                                        <input class="form-control" type="text" v-model="currentProduct.batch" @focus="focusinput" inputmode="numeric" @input="onlydigitsbatch">
                                    </div>
                                </div>
                            </div>
                            <div v-if="currentProduct.ings.length">
                                <p>Расход на партию</p>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr class="row m-0">
                                                <th class="d-inline-block col-6">наимен-е</th>
                                                <th class="d-inline-block col-2 text-center">ед.изм.</th>
                                                <th class="d-inline-block col-2 text-center">кол-во</th>
                                                <th class="d-inline-block col-2 text-center">ст-ть</th>
                                            </tr>
                                        </thead>
                                        <tr v-for="ing, i in currentProduct.ings" class="row m-0 removeTr">
                                            <td class="d-inline-block col-6">@{{ ing.ing.name.toLowerCase() }}</td>
                                            <td class="d-inline-block col-2 text-center">@{{ ing.ing.name == 'Электроэнергия' ? 'квт/ч' : 'гр' }}</td>
                                            <td class="d-inline-block col-2">
                                                <input type="text" v-model="ing.kol" @focus="focusinput" @input="onlydigitskol($event, i)" inputmode="numeric" style="text-align:center; width: 50px; padding: 0; height: auto; line-height: 1" class="form-control">
                                            </td>
                                            <td class="d-inline-block col-2 text-center">
                                                @{{ getCostPerIng(ing) }}
                                                <span class="removeItem" @click="currentProduct.ings.splice(i,1)">x</span>
                                            </td>
                                        </tr>
                                        <tr style="background: none">
                                            <td colspan="3" class="d-inline-block col-10 text-right p-0">Итого затраты:</td>
                                            <td class="d-inline-block col-2 font-weight-bold p-0 text-center">@{{ currentCostPrice.allCost }}</td>
                                        </tr>
                                        <tr style="background: none">
                                            <td colspan="3" class="d-inline-block col-10 text-right border-0 p-0">Затраты на 1 изделие:</td>
                                            <td class="d-inline-block col-2 border-0 font-weight-bold p-0 text-center">@{{ currentCostPrice.costPerOne }}</td>
                                        </tr>
                                        <tr style="background: none">
                                            <td colspan="3" class="d-inline-block col-10 text-right border-0 p-0">
                                                <div class="form-check">
                                                    <span>Наценка (</span>
                                                    <label style="cursor: pointer" for="useDefMarja" class="form-check-label">использовать общую </label>
                                                    <input type="checkbox" v-model="currentProduct.useDefMarja" class="form-check-input ml-0" id="useDefMarja">
                                                    <span>):</span>
                                                </div>
                                            </td>
                                            <td class="d-inline-block col-2 border-0 font-weight-bold p-0 text-center">
                                                <span class="marja">
                                                    <input type="text" v-model="currentProduct.marja" @focus="focusinput" @input="onlypercent($event)" inputmode="numeric" class="form-control" :disabled="currentProduct.useDefMarja">
                                                </span>
                                            </td>
                                        </tr>
                                        <tr style="background: none">
                                            <td colspan="3" class="d-inline-block col-10 text-right border-0 p-0">Отпускная цена:</td>
                                            <td class="d-inline-block col-2 border-0 font-weight-bold p-0 text-center">@{{ currentCostPrice.price }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="form-row align-items-center">
                                <div class="col col-auto">
                                    <span>Добавить сырье</span>
                                </div>
                                <div class="col">
                                    <select v-model="newIng" class="form-control">
                                        <option value="" disabled>Выбери</option>
                                        <option v-for="item,i in items" :value="i" v-if="currentProduct.ings.map(ing => ing.ing.id).indexOf(item.id) < 0">@{{item.name}}</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div class="row w-100 ml-0 mr-0">
                            <div class="col col-12 col-md-auto pl-0 pr-0 mb-3 order-12 order-md-1" v-if="currentProduct.id">
                                <div class="w-100 d-block d-md-none mb-3"></div>
                                <button class="btn btn-danger btn-block" type="button" data-dismiss="modal" :disabled="disabled" @click="delproduct(currentProduct.id)">Удалить</button>
                            </div>
                            <div class="col d-none d-md-block order-md-2"></div>
                            <div class="col col-12 col-md-auto pl-0 pr-0 pr-md-2 mb-3 order-6">
                                <button class="btn btn-light btn-block" type="button" data-dismiss="modal">Отмена</button>
                            </div>
                            <div class="col col-12 col-md-auto pl-0 pr-0 mb-3 order-1 order-md-12">
                                <button class="btn btn-primary btn-block" type="button" @click="addEdit(currentProduct)" :disabled="disabled">@{{ currentProduct.id ? 'Сохранить' : 'Добавить' }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="js/products.js"></script>
@endsection
