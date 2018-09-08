import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import {CATEGORY_CHANGED} from '../actions';
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

        return <div className="c-red-500 form-text text-muted">{errors[key]}</div>
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.PartnerCategoryEdit

        return <div className="bgc-white bd bdrs-3 p-20 mB-20">

            <div className="row">
                <div className="col">
                    <h4 className="page-title">
                        {translator('navigation_categories')}&nbsp;/
                        &nbsp;{isLoading ? <i className="fa fa-spin fa-circle-o-notch"/> : <span>#{model.id}&nbsp;{model.category.name}</span>}
                    </h4>
                </div>
                <div className="col text-right">
                    {model.id && <button className="btn btn-danger btn-sm mr-2"
                                         disabled={isLoading}
                                         onClick={this.remove}>
                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-times"}/>
                        &nbsp;{translator('remove')}
                    </button>}

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

                    <div className="form-group">
                        <label className={model.hasPrice ? 'required' : ''}>{translator('price')}</label>
                        <input type="number"
                               name="price"
                               min={0}
                               step={1}
                               className="form-control"
                               onChange={this.changeInt('price')}
                               value={model.price !== null ? model.price : ''}/>
                        {this.getError('price')}
                        <div className="text-muted">
                            <i className="fa fa-info-circle"/>&nbsp;{translator('category_price_notice')}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PartnerCategoryEdit))
