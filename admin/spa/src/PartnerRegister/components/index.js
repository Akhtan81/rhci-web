import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import {ADD_CATEGORY, ADD_POSTAL_CODE, MODEL_CHANGED, REMOVE_CATEGORY, REMOVE_POSTAL_CODE} from '../actions';
import selectors from './selectors';
import FetchCategories from '../actions/FetchCategories';
import FetchOrderTypes from '../actions/FetchOrderTypes';
import FetchCountries from '../actions/FetchCountries';
import Save from '../actions/Save';
import translator from '../../translations/translator';
import {cid, objectValues, setTitle} from "../../Common/utils";
import Logo from "../../Common/components/Logo";
import Lang from "../../Common/components/Lang";

class PartnerRegister extends React.Component {

    componentWillMount() {
        setTitle(translator('navigation_partners_register'))

        this.props.dispatch(FetchOrderTypes())
        this.props.dispatch(FetchCategories())
        this.props.dispatch(FetchCountries())
    }

    submit = () => {
        const {model} = this.props.PartnerRegister

        this.props.dispatch(Save(model))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeBool = name => e => this.change(name, e.target.checked)

    changeString = name => e => this.change(name, e.target.value)

    changeInt = name => e => {
        let value = parseInt(e.target.value)
        if (isNaN(value)) value = null

        this.change(name, value)
    }

    changeRequestType = (cid) => e => {

        let value = e.target.value
        if (value === 'none') value = null

        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                request: {
                    cid,
                    type: value
                }
            }
        })
    }

    changeRequestPostalCode = cid => e => {

        const postalCode = e.target.value.replace(/[^0-9]/g, '').trim()

        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                request: {
                    cid,
                    postalCode
                }
            }
        })
    }

    changeCategory = cid => e => {

        let id = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(id) || id < 0) id = 0

        this.props.dispatch({
            type: MODEL_CHANGED,
            payload: {
                requestedCategory: {
                    cid,
                    category: id
                }
            }
        })
    }

    removePostalCode = cid => () => {

        this.props.dispatch({
            type: REMOVE_POSTAL_CODE,
            payload: {
                cid,
            }
        })
    }

    removeCategory = cid => () => {

        this.props.dispatch({
            type: REMOVE_CATEGORY,
            payload: {
                cid,
            }
        })
    }

    addPostalCode = () => {

        this.props.dispatch({
            type: ADD_POSTAL_CODE,
            payload: {
                cid: cid(),
                postalCode: null,
                type: null
            }
        })
    }

    addCategory = () => {

        this.props.dispatch({
            type: ADD_CATEGORY,
            payload: {
                cid: cid(),
                category: null,
            }
        })
    }

    getError = key => {
        const {errors} = this.props.PartnerRegister.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    getCategoryError = (cid) => {
        const {errors} = this.props.PartnerRegister.validator

        if (errors.requestedCategories === undefined) return null
        if (errors.requestedCategories[cid] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors.requestedCategories[cid]}</small>
    }

    getCodeError = (cid) => {
        const {errors} = this.props.PartnerRegister.validator

        if (errors.requestedPostalCodes === undefined) return null
        if (errors.requestedPostalCodes[cid] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors.requestedPostalCodes[cid]}</small>
    }

    renderPostalCodes() {

        const {model} = this.props.PartnerRegister
        const {items} = this.props.PartnerRegister.OrderTypes

        const requests = objectValues(model.requestedPostalCodes)

        return <div className="row">

            {requests.map((request, i) => {

                return <div key={i}
                            className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">

                    <div className="form-group">
                        <div className="input-group">

                            {requests.length > 1 ?
                                <div className="input-group-prepend">
                                    <button className="btn btn-outline-secondary"
                                            onClick={this.removePostalCode(request.cid)}>
                                        <i className="fa fa-times"/>
                                    </button>
                                </div> : null}

                            <input type="text"
                                   name="postalCode"
                                   className="form-control"
                                   placeholder={translator('postal_code')}
                                   onChange={this.changeRequestPostalCode(request.cid)}
                                   value={request.postalCode || ""}/>

                            <div className="input-group-append">
                                <select name="type"
                                        value={request.type || 'none'}
                                        onChange={this.changeRequestType(request.cid)}
                                        className="form-control">
                                    <option value="none" disabled={true}>{translator("select_type")}</option>

                                    {items.map((item, i) =>
                                        <option
                                            key={i}
                                            value={item.key}
                                            disabled={item.disabled === true}>{item.name}</option>
                                    )}

                                </select>
                            </div>
                        </div>
                        {this.getCodeError(request.cid)}
                    </div>

                </div>
            })}
        </div>
    }

    renderCategories() {

        const {model} = this.props.PartnerRegister
        const {items} = this.props.PartnerRegister.Categories

        const requests = objectValues(model.requestedCategories)

        return <div className="row">

            {requests.map((request, i) => {

                return <div key={i}
                            className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">

                    <div className="form-group">
                        <div className="input-group">

                            {requests.length > 1 ?
                                <div className="input-group-prepend">
                                    <button className="btn btn-outline-secondary"
                                            onClick={this.removeCategory(request.cid)}>
                                        <i className="fa fa-times"/>
                                    </button>
                                </div> : null}

                            <select name="type"
                                    value={request.category || 'none'}
                                    onChange={this.changeCategory(request.cid)}
                                    className="form-control">
                                <option value="none" disabled={true}>{translator("select_category")}</option>

                                {items.map((item, i) => {
                                    let lvl = ''
                                    for (let i = 0; i < item.lvl; i++) {
                                        lvl += ' - '
                                    }

                                    const disabled = !!requests.find(request => request.category === item.id)

                                    return <option
                                        key={i}
                                        disabled={disabled}
                                        value={item.id}>{lvl}{item.name}</option>
                                })}

                            </select>
                        </div>
                        {this.getCategoryError(request.cid)}
                    </div>

                </div>
            })}
        </div>
    }

    renderContent = () => {

        const {model, isValid, isLoading, serverErrors} = this.props.PartnerRegister
        const {items} = this.props.PartnerRegister.Countries

        const containsRecycling = !!objectValues(model.requestedPostalCodes).find(request => request.type === 'recycling')

        return <div className="row">

            <div className="col-12">
                <h2 className="text-center">{translator('navigation_partners_register')}</h2>
                <p className="text-center">{translator('navigation_partners_register_description')}</p>

                <p>{translator('register_already_user')}
                    &nbsp;<Link to="/login">{translator('signin')}</Link></p>

                {serverErrors.length > 0 && <div className="alert alert-danger">
                    <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                </div>}
            </div>

            <div className="col-12">

                <div className="row">
                    <div className="col-12">

                        <h4>{translator('personal_information')}</h4>

                        <div className="row">
                            <div className="col">
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
                            <div className="col-12 col-md-6">
                                <div className="form-group">
                                    <label
                                        className={!model.id ? "required" : ""}>{translator('password')}</label>
                                    <input type="password"
                                           name="password"
                                           className="form-control"
                                           onChange={this.changeString('password')}
                                           value={model.user.password || ''}/>
                                    {this.getError('password')}
                                </div>
                            </div>

                            <div className="col-12 col-md-6">
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
                    <div className="col-12">

                        <h4>{translator('partner_location')}</h4>

                        <div className="row">
                            <div className="col-12">
                                <div className="form-group">
                                    <label className="required">{translator('country')}</label>
                                    <select
                                        name="country"
                                        className="form-control"
                                        onChange={this.changeInt('country')}
                                        value={model.country || -1}>
                                        <option value={-1} disabled={true}>{translator('select_value')}</option>
                                        {items.map((item, key) =>
                                            <option key={key} value={item.id}>{item.name}</option>
                                        )}
                                    </select>
                                    {this.getError('country')}
                                </div>
                            </div>

                            <div className="col-12">
                                <div className="form-group">
                                    <label className="required">{translator('address')}</label>
                                    <textarea
                                        name="address"
                                        className="form-control"
                                        onChange={this.changeString('address')}
                                        value={model.location.address || ''}/>
                                    {this.getError('address')}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="row">


                    <div className="col-12">

                        <h4 className="text-center">{translator('partner_postal_codes')}</h4>

                        {this.renderPostalCodes()}

                        <div className="row">
                            <div className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">
                                <div className="form-group text-right">
                                    <button className="btn btn-sm btn-outline-success"
                                            onClick={this.addPostalCode}>
                                        <i className="fa fa-plus"/>&nbsp;{translator('add')}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {containsRecycling
                    ? <div className="row">
                        <div className="col-12">

                            <h4 className="text-center">
                                <i className="fa fa-recycle"/>&nbsp;{translator('partner_register_recycling')}
                            </h4>

                            {this.renderCategories()}

                            <div className="row">
                                <div className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">
                                    <div className="form-group text-right">
                                        <button className="btn btn-sm btn-outline-success"
                                                onClick={this.addCategory}>
                                            <i className="fa fa-plus"/>&nbsp;{translator('add')}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    : null}
            </div>

            <div className="col-12">
                <div className="form-group text-center">
                    <label>
                        <input type="checkbox"
                               name="isAccepted"
                               onChange={this.changeBool('isAccepted')}
                               checked={model.isAccepted}/>
                        &nbsp;I have read&nbsp;
                        <a href={AppRouter.GET.legalPrivacyIndex} target="_blank">
                            {translator('navigation_privacy')}
                            &nbsp;<i className="fa fa-external-link"/>
                        </a>
                        ,&nbsp;
                        <a href={AppRouter.GET.legalOfferIndex} target="_blank">
                            {translator('navigation_public_offer')}
                            &nbsp;<i className="fa fa-external-link"/>
                        </a>
                        <br/>&nbsp;and accept&nbsp;
                        <a href={AppRouter.GET.legalTermsIndex} target="_blank">
                            {translator('navigation_terms')}
                            &nbsp;<i className="fa fa-external-link"/>
                        </a>
                    </label>
                </div>
                <div className="form-group text-center">
                    <button className="btn btn-lg btn-success"
                            onClick={this.submit}
                            disabled={isLoading || !isValid}>
                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                        &nbsp;{translator('signup')}
                    </button>
                </div>
            </div>
        </div>
    }

    render() {

        const {isSaveSuccess} = this.props.PartnerRegister

        return <div className="container">

            <div className="lang-container">
                <Lang/>
            </div>

            <Logo/>

            <div className="row">
                <div className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">
                    <div className="card shadow-sm my-4">
                        <div className="card-body">

                            {isSaveSuccess
                                ? <div className="text-center my-5">
                                    <h3>
                                        <i className="fa fa-check-circle" style={{color: 'green'}}/>
                                        &nbsp;{translator('partner_signup_success_title')}
                                    </h3>
                                    <h4>{translator('partner_signup_success_footer')}</h4>
                                </div>
                                : this.renderContent()}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PartnerRegister))
