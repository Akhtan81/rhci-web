import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import {MODEL_CHANGED, FETCH_SUCCESS} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import FetchCountries from "../../Partner/actions/FetchCountries";

class PartnerEdit extends React.Component {

    componentWillMount() {

        const {Country} = this.props
        if (!Country.isLoading && Country.items.length === 0) {
            this.props.dispatch(FetchCountries())
        }

        const {id} = this.props.match.params
        if (id > 0) {
            this.props.dispatch(FetchItem(id))
        } else {
            this.props.dispatch({
                type: FETCH_SUCCESS,
                payload: {}
            })
        }
    }

    submit = () => {
        const {model} = this.props.PartnerEdit

        this.props.dispatch(Save(model))
    }

    change = (key, value) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeSelect = name => e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value) || value < 0) value = null;

        this.change(name, value)
    }

    changeBool = name => e => this.change(name, e.target.checked)

    changeString = name => e => this.change(name, e.target.value)

    changeInt = name => e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value)) value = 0;

        this.change(name, value)
    }

    changeParent = e => {
        let match = null

        let id = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (!isNaN(id) && id > 0) {
            match = id
        }

        this.change('parent', match)
    }

    uploadAvatar = (e) => {
        const file = e.target.files[0]

    }

    getError = key => {
        const {errors} = this.props.PartnerEdit.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.PartnerEdit
        const {Country, City, Region, District} = this.props

        return <div className="bgc-white bd bdrs-3 p-20 mB-20">

            <div className="row">
                <div className="col">
                    <h4 className="page-title">
                        {translator('navigation_partners')}&nbsp;/&nbsp;
                        {model.id > 0 ? <span>#{model.id}&nbsp;{model.user.name}</span> :
                            <span>{translator('create')}</span>}
                    </h4>
                </div>
                <div className="col text-right">
                    {model.id && model.user.isActive
                        ? <button className="btn btn-outline-danger btn-sm mr-2"
                                  disabled={isLoading}>
                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-down"}/>
                            &nbsp;{translator('deactivate')}
                        </button>
                        : null}

                    {model.id && !model.user.isActive
                        ? <button className="btn btn-outline-success btn-sm mr-2"
                                  disabled={isLoading}>
                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-up"}/>
                            &nbsp;{translator('activate')}
                        </button>
                        : null}

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
                        <label className="required">{translator('name')}</label>
                        <input type="text"
                               name="name"
                               className="form-control"
                               onChange={this.changeString('name')}
                               value={model.user.name || ''}/>
                        {this.getError('name')}
                    </div>

                    <div className="form-group">
                        <label className="required">{translator('avatar')}</label>
                        <input type="file"
                               name="avatar"
                               className="form-control"
                               onChange={this.uploadAvatar}/>
                        {this.getError('avatar')}
                    </div>

                    <div className="form-group">
                        <label className="required">{translator('email')}</label>
                        <input type="text"
                               name="email"
                               className="form-control"
                               onChange={this.changeString('email')}
                               value={model.user.email || ''}/>
                        {this.getError('email')}
                    </div>

                    <div className="form-group">
                        <label className="required">{translator('phone')}</label>
                        <input type="text"
                               name="phone"
                               className="form-control"
                               onChange={this.changeString('phone')}
                               value={model.user.phone || ''}/>
                        {this.getError('phone')}
                    </div>

                    <div className="form-group">
                        <label className="required">{translator('country')}</label>
                        <select name="country"
                                className="form-control"
                                onChange={this.changeSelect('country')}
                                value={model.country || -1}>
                            <option value={-1}>{translator('select_country')}</option>
                            {Country.items.map((item, i) => <option key={i} value={item.id}>{item.name}</option>)}
                        </select>
                        {this.getError('country')}
                    </div>

                    <div className="form-group">
                        <label className="required">{translator('region')}</label>
                        <select name="region"
                                disabled={!model.country}
                                className="form-control"
                                onChange={this.changeSelect('region')}
                                value={model.region || -1}>
                            <option value={-1}>{translator('select_region')}</option>
                            {Region.items.map((item, i) => <option key={i} value={item.id}>{item.name}</option>)}
                        </select>
                        {this.getError('region')}
                    </div>

                    <div className="form-group">
                        <label className="required">{translator('city')}</label>
                        <select name="city"
                                disabled={!model.region}
                                className="form-control"
                                onChange={this.changeSelect('city')}
                                value={model.city || -1}>
                            <option value={-1}>{translator('select_city')}</option>
                            {City.items.map((item, i) => <option key={i} value={item.id}>{item.name}</option>)}
                        </select>
                        {this.getError('city')}
                    </div>

                    <div className="form-group">
                        <label className="required">{translator('district')}</label>
                        <select name="district"
                                disabled={!model.city}
                                className="form-control"
                                onChange={this.changeSelect('district')}
                                value={model.district || -1}>
                            <option value={-1}>{translator('select_district')}</option>
                            {District.items.map((item, i) => <option key={i}
                                                                     value={item.id}>{item.postalCode + " | " + item.name}</option>)}
                        </select>
                        {this.getError('district')}
                    </div>

                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PartnerEdit))
