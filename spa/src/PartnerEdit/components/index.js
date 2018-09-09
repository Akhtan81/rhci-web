import React from 'react';
import {connect} from 'react-redux';
import {withRouter, Redirect} from 'react-router-dom';
import {MODEL_CHANGED, FETCH_SUCCESS} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import UploadMedia from '../actions/UploadMedia';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import FetchCountries from "../../Partner/actions/FetchCountries";

class PartnerEdit extends React.Component {

    state = {
        canRedirect: false
    }

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

        this.props.dispatch(Save(model, () => {
            this.setState({canRedirect: true})
        }))
    }

    deactivate = () => {

        if (!confirm(translator('confirm_partner_deactivation'))) return;

        const {model} = this.props.PartnerEdit

        this.props.dispatch(Save({
            id: model.id,
            user: {
                isActive: false
            }
        }))
    }

    activate = () => {

        const {model} = this.props.PartnerEdit

        this.props.dispatch(Save({
            id: model.id,
            user: {
                isActive: true
            }
        }))

    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeDistrict = e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value) || value < 0) {
            value = null;
        } else {
            const {items} = this.props.District

            value = items.find(item => item.id === value);
        }

        this.change('district', value)
    }

    changeRegion = e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value) || value < 0) {
            value = null;
        } else {
            const {items} = this.props.Region

            value = items.find(item => item.id === value);
        }

        this.change('region', value)
    }

    changeCountry = e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value) || value < 0) {
            value = null;
        } else {
            const {items} = this.props.Country

            value = items.find(item => item.id === value);
        }

        this.change('country', value)
    }

    changeCity = e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value) || value < 0) {
            value = null;
        } else {
            const {items} = this.props.City

            value = items.find(item => item.id === value);
        }

        this.change('city', value)
    }

    changeString = name => e => this.change(name, e.target.value)

    uploadAvatar = (e) => {
        const file = e.target.files[0]
        if (!file) return

        this.props.dispatch(UploadMedia(file))
    }

    getError = key => {
        const {errors} = this.props.PartnerEdit.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.PartnerEdit
        const {Country, City, Region, District} = this.props

        if (this.state.canRedirect) {
            return <Redirect to="/partners"/>
        }

        return <div className="bgc-white bd bdrs-3 p-20 mB-20">

            <div className="row mb-3">
                <div className="col-12 col-lg-8">
                    <h4 className="page-title">
                        {translator('navigation_partners')}&nbsp;/&nbsp;
                        {model.id > 0 ? <span>#{model.id}&nbsp;{model.user.name}</span> :
                            <span>{translator('create')}</span>}
                    </h4>
                    {model.originalDistrict ?
                        <h5>{model.originalDistrict.postalCode + " | " + model.originalDistrict.fullName}</h5> : null}
                </div>
                <div className="col-12 col-lg-4 text-right">
                    {model.id && model.user.isActive
                        ? <button className="btn btn-outline-danger btn-sm mr-2"
                                  onClick={this.deactivate}
                                  disabled={isLoading}>
                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-down"}/>
                            &nbsp;{translator('deactivate')}
                        </button>
                        : null}

                    {model.id && !model.user.isActive
                        ? <button className="btn btn-outline-success btn-sm mr-2"
                                  onClick={this.activate}
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

                    <div className="row">
                        <div className="col-12 col-sm-4 col-md-3 col-lg-2">

                            <div className="img-container">
                                {!isLoading && model.user.avatar
                                    ? <img src={model.user.avatar.url} className="img-fluid"/>
                                    : null}
                            </div>

                            <div className="form-group">
                                <label>{translator('avatar')}</label>
                                <input type="file"
                                       name="avatar"
                                       className="form-control"
                                       accept="image/png,image/jpg,image/jpeg,image/gif,image/bmp"
                                       onChange={this.uploadAvatar}/>
                                {this.getError('avatar')}
                            </div>

                        </div>
                        <div className="col-12 col-sm-8 col-md-9 col-lg-10">
                            <div className="row">
                                <div className="col-12">
                                    <div className="form-group">
                                        <label className="required">{translator('name')}</label>
                                        <input type="text"
                                               name="name"
                                               className="form-control"
                                               onChange={this.changeString('name')}
                                               value={model.user.name || ''}/>
                                        {this.getError('name')}
                                    </div>
                                </div>
                                <div className="col">
                                    <div className="form-group">
                                        <label className="required">{translator('email')}</label>
                                        <input type="email"
                                               name="email"
                                               className="form-control"
                                               onChange={this.changeString('email')}
                                               value={model.user.email || ''}/>
                                        {this.getError('email')}
                                    </div>
                                </div>
                                <div className="col">

                                    <div className="form-group">
                                        <label>{translator('phone')}</label>
                                        <input type="text"
                                               name="phone"
                                               className="form-control"
                                               onChange={this.changeString('phone')}
                                               value={model.user.phone || ''}/>
                                        {this.getError('phone')}
                                    </div>
                                </div>
                            </div>


                            <div className="row">
                                <div className="col-12 col-sm-6">

                                    <div className="form-group">
                                        <label className={!model.id ? "required" : ""}>{translator('password')}</label>
                                        <input type="password"
                                               name="password"
                                               className="form-control"
                                               onChange={this.changeString('password')}
                                               value={model.user.password || ''}/>
                                        {this.getError('password')}
                                    </div>

                                </div>
                                <div className="col-12 col-sm-6">

                                    <div className="form-group">
                                        <label
                                            className={!model.id ? "required" : ""}>{translator('password_repeat')}</label>
                                        <input type="password"
                                               name="password2"
                                               className="form-control"
                                               onChange={this.changeString('password2')}
                                               value={model.user.password2 || ''}/>
                                        {this.getError('password2')}
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                    <div className="row">
                        <div className="col-12 col-sm-4">

                            <div className="form-group">
                                <label className="required">{translator('country')}</label>
                                <select name="country"
                                        className="form-control"
                                        onChange={this.changeCountry}
                                        value={model.country ? model.country.id : -1}>
                                    <option value={-1}>{translator('select_country')}</option>
                                    {Country.items.map((item, i) =>
                                        <option key={i} value={item.id}>{item.name}</option>)}
                                </select>
                                {this.getError('country')}
                            </div>

                        </div>
                        <div className="col-12 col-sm-4">

                            <div className="form-group">
                                <label className="required">{translator('region')}</label>
                                <select name="region"
                                        disabled={!model.country}
                                        className="form-control"
                                        onChange={this.changeRegion}
                                        value={model.region ? model.region.id : -1}>
                                    <option value={-1}>{translator('select_region')}</option>
                                    {Region.items.map((item, i) =>
                                        <option key={i} value={item.id}>{item.name}</option>)}
                                </select>
                                {this.getError('region')}
                            </div>

                        </div>
                        <div className="col-12 col-sm-4">

                            <div className="form-group">
                                <label className="required">{translator('city')}</label>
                                <select name="city"
                                        disabled={!model.region}
                                        className="form-control"
                                        onChange={this.changeCity}
                                        value={model.city ? model.city.id : -1}>
                                    <option value={-1}>{translator('select_city')}</option>
                                    {City.items.map((item, i) =>
                                        <option key={i} value={item.id}>{item.name}</option>)}
                                </select>
                                {this.getError('city')}
                            </div>

                        </div>
                        <div className="col-12">

                            <div className="form-group">
                                <label className="required">{translator('district')}</label>
                                <select name="district"
                                        disabled={!model.city}
                                        className="form-control"
                                        onChange={this.changeDistrict}
                                        value={model.district ? model.district.id : -1}>
                                    <option value={-1}>{translator('select_district')}</option>

                                    {model.district && District.items.length === 0
                                        ? <option
                                            value={model.district.id}>{model.district.postalCode + " | " + model.district.name}</option>
                                        : null}

                                    {District.items.map((item, i) =>
                                        <option key={i} value={item.id}>{item.postalCode + " | " + item.name}</option>)}
                                </select>
                                {this.getError('district')}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PartnerEdit))
