import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import {CATEGORY_CHANGED} from '../actions';
import {OrderTypes} from '../../CategoryEdit/components';
import selectors from './selectors';
import SaveCategory from '../actions/SaveCategory';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';

class PartnerCategoryEdit extends React.Component {

    componentWillMount() {

        const {id} = this.props.match.params
        if (id > 0) {
            this.props.dispatch(FetchItem(id))
        }
    }

    submit = () => {
        const {model} = this.props.PartnerCategoryEdit

        this.props.dispatch(SaveCategory(model))
    }

    change = (key, value) => this.props.dispatch({
        type: CATEGORY_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeInt = name => e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value)) value = 0;

        this.change(name, value)
    }

    getError = key => {
        const {errors} = this.props.PartnerCategoryEdit.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.PartnerCategoryEdit

        const type = model.category ? OrderTypes.find(e => e.value === model.category.type) : null

        return <div className="bgc-white bd bdrs-3 p-20 mB-20">

            <div className="row mb-3">
                <div className="col">
                    <h4 className="page-title">
                        {translator('navigation_categories')}&nbsp;/
                        &nbsp;{!model.id
                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                        : <span>#{model.id}&nbsp;{model.category.name}</span>}
                    </h4>
                </div>
                <div className="col text-right">
                    <button className="btn btn-success btn-sm"
                            disabled={!isValid || isLoading}
                            onClick={this.submit}>
                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                        &nbsp;{translator('save')}
                    </button>

                    {isSaveSuccess && <div className="text-muted c-green-500">
                        <i className="fa fa-check"/>&nbsp;{translator('save_success_alert')}
                    </div>}
                </div>
            </div>

            <div className="row">
                <div className="col">

                    {serverErrors.length > 0 && <div className="alert alert-danger">
                        <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                    </div>}

                    <table className="table table-sm">
                        <tbody>
                        <tr>
                            <th className="align-middle">{translator('created_at')}</th>
                            <td className="align-middle">{model.createdAt}</td>
                        </tr>
                        <tr>
                            <th className="align-middle">{translator('type')}</th>
                            <td className="align-middle">{type ? type.label : ''}</td>
                        </tr>
                        <tr>
                            <th className="align-middle">{translator('locale')}</th>
                            <td className="align-middle">{model.category ? model.category.locale : ''}</td>
                        </tr>
                        <tr>
                            <th className="align-middle">{translator('is_selectable')}</th>
                            <td className="align-middle"><i className={"fa " + (model.category && model.category.isSelectable ? 'fa-check c-green-500' : 'fa-ban c-red-500')}/></td>
                        </tr>
                        <tr>
                            <th className="align-middle">{translator('has_price')}</th>
                            <td className="align-middle"><i className={"fa " + (model.category && model.category.hasPrice ? 'fa-check c-green-500' : 'fa-ban c-red-500')}/></td>
                        </tr>
                        <tr>
                            <th className="align-middle">{translator('price')}</th>
                            <td className="align-middle">
                                <div className="input-group input-group-sm">
                                    <input type="number"
                                           name="price"
                                           min={0}
                                           step={1}
                                           title={translator('category_partner_price')}
                                           placeholder={translator('category_partner_price')}
                                           className="form-control w-50"
                                           onChange={this.changeInt('price')}
                                           value={model.price !== null ? model.price : ''}/>
                                    <div className="input-group-append w-50">
                                        <input type="number"
                                               name="originalPrice"
                                               readOnly={true}
                                               title={translator('category_original_price')}
                                               placeholder={translator('category_original_price')}
                                               className="form-control w-100"
                                               value={model.category && model.category.hasPrice ? model.category.price : ''}/>
                                    </div>
                                </div>
                                {this.getError('price')}
                                <small className="text-muted d-block">
                                    <i className="fa fa-info-circle"/>&nbsp;{translator('category_price_notice')}
                                </small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PartnerCategoryEdit))
