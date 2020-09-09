import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';

import selectors from './selectors';
import FetchItem from '../actions/FetchItem';
import FetchCountries from '../actions/FetchCountries';
import Save from '../actions/Save';
import UploadMedia from "../actions/UploadMedia";
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";
import {MODEL_CHANGED, TOGGLE_REQUESTED_CATEGORIES_MODAL, TOGGLE_REQUESTED_CODES_MODAL} from "../actions";

import PaymentInfo from "./PaymentInfo";
import RequestedCodesModal from './RequestedCodesModal';
import RequestedCategoriesModal from './RequestedCategoriesModal';

class ProfilePartner extends React.Component {

    componentWillMount() {

        setTitle(translator('navigation_profile'))

        this.props.dispatch(FetchItem())
        this.props.dispatch(FetchCountries())
    }

    getError = key => {
        const {errors} = this.props.ProfilePartner.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    submit = () => {
        const {model} = this.props.ProfilePartner

        this.props.dispatch(Save(model))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeString = name => e => this.change(name, e.target.value)

    changeCountry = e => {
        let value = parseInt(e.target.value)
        if (isNaN(value)) value = null

        this.change('country', {
            id: value
        })
    }

    uploadAvatar = (e) => {
        const file = e.target.files[0]
        if (!file) return

        this.props.dispatch(UploadMedia(file))
    }

    toggleModal = () => {
        this.props.dispatch({
            type: TOGGLE_REQUESTED_CODES_MODAL
        })
    }

    toggleCategoryModal = () => {
        this.props.dispatch({
            type: TOGGLE_REQUESTED_CATEGORIES_MODAL
        })
    }

    renderRequestedPostalCodes() {

        const {model} = this.props.ProfilePartner

        if (model.requests.length === 0) return <div>{translator('no_requested_postal_codes')}</div>

        return <ul className="simple">{model.requests.map((item, i) =>
            <li key={i}>{item.postalCode} - {translator('order_types_' + item.type)}</li>)}
        </ul>
    }

    renderRequestedCategories() {

        const {model} = this.props.ProfilePartner

        if (model.requestedCategories.length === 0) return <div>{translator('no_requested_categories')}</div>

        return <ul className="simple">{model.requestedCategories.map((item, i) => {

            let statusClass = ''
            if (item.status === 'approved') {
                statusClass = 'c-green-500'
            } else if (item.status === 'rejected') {
                statusClass = 'c-red-500'
            }

            return <li key={i} className={statusClass}>
                {item.status === 'created' ? <i className="fa fa-clock-o"/> : null}
                {item.status === 'approved' ? <i className="fa fa-check"/> : null}
                {item.status === 'rejected' ? <i className="fa fa-ban"/> : null}
                &nbsp;{translator('order_types_' + item.category.type)} - {item.category.name}
            </li>
        })}
        </ul>
    }

    renderAssignedPostalCodes() {

        const {model} = this.props.ProfilePartner

        const canManageJunkRemovalOrders = model.canManageJunkRemovalOrders
        const canManageRecyclingOrders = model.canManageRecyclingOrders
        const canManageDonationOrders = model.canManageDonationOrders
        const canManageShreddingOrders = model.canManageShreddingOrders
        const canManagebusybeeOrders = model.canManagebusybeeOrders
        const cardStyle= {maxHeight: '500px', overflow: 'auto'}

        return <div className="row">

            <div className="col-12 col-md-6 col-lg-3">

                <div className="card mb-3">
                    <div className="card-header">
                        <h5 className="m-0"><i className="fa fa-cubes"/>&nbsp;{translator('order_types_junk_removal')}
                        </h5>
                    </div>
                    <div className="card-body">
                        {!canManageJunkRemovalOrders ? <p className="c-red-500">
                            <i className="fa fa-warning"/>&nbsp;{translator('no_account_for_junk_removal')}
                        </p> : null}

                        {model.postalCodesJunkRemoval.length > 0
                            ? <ul className="simple" style={cardStyle}>{model.postalCodesJunkRemoval.map((item, i) =>
                                <li key={i}><i
                                    className={"fa " + (canManageJunkRemovalOrders ? "fa-check" : "fa-lock")}/>&nbsp;{item}
                                </li>)}
                            </ul>
                            : <span>{translator('no_assigned_postal_codes')}</span>}
                    </div>
                </div>

            </div>

            <div className="col-12 col-md-6 col-lg-3">

                <div className="card mb-3">
                    <div className="card-header">
                        <h5 className="m-0"><i className="fa fa-recycle"/>&nbsp;{translator('order_types_recycling')}
                        </h5>
                    </div>
                    <div className="card-body">

                        {!canManageRecyclingOrders ? <p className="c-red-500">
                            <i className="fa fa-warning"/>&nbsp;{translator('no_account_recycling')}
                        </p> : null}

                        {model.postalCodesRecycling.length > 0
                            ? <ul className="simple" style={cardStyle}>{model.postalCodesRecycling.map((item, i) =>
                                <li key={i}><i
                                    className={"fa " + (canManageRecyclingOrders ? "fa-check" : "fa-lock")}/>&nbsp;{item}
                                </li>)}
                            </ul>
                            : <span>{translator('no_assigned_postal_codes')}</span>}
                    </div>
                </div>

            </div>

            <div className="col-12 col-md-6 col-lg-3">

                <div className="card mb-3">
                    <div className="card-header">
                        <h5 className="m-0"><i className="fa fa-gift"/>&nbsp;{translator('order_types_donation')}</h5>
                    </div>
                    <div className="card-body">

                        {!canManageDonationOrders ? <p className="c-red-500">
                            <i className="fa fa-warning"/>&nbsp;{translator('no_account_for_donation')}
                        </p> : null}


                        {model.postalCodesDonation.length > 0
                            ? <ul className="simple" style={cardStyle}>{model.postalCodesDonation.map((item, i) =>
                                <li key={i}><i
                                    className={"fa " + (canManageDonationOrders ? "fa-check" : "fa-lock")}/>&nbsp;{item}
                                </li>)}
                            </ul>
                            : <span>{translator('no_assigned_postal_codes')}</span>}
                    </div>
                </div>

            </div>

            <div className="col-12 col-md-6 col-lg-3">

                <div className="card mb-3">
                    <div className="card-header">
                        <h5 className="m-0"><i
                            className="fa fa-stack-overflow"/>&nbsp;{translator('order_types_shredding')}</h5>
                    </div>
                    <div className="card-body">

                        {!canManageShreddingOrders ? <p className="c-red-500">
                            <i className="fa fa-warning"/>&nbsp;{translator('no_account_for_shredding')}
                        </p> : null}


                        {model.postalCodesShredding.length > 0
                            ? <ul className="simple" style={cardStyle}>{model.postalCodesShredding.map((item, i) =>
                                <li key={i}><i
                                    className={"fa " + (canManageShreddingOrders ? "fa-check" : "fa-lock")}/>&nbsp;{item}
                                </li>)}
                            </ul>
                            : <span>{translator('no_assigned_postal_codes')}</span>}
                    </div>
                </div>

            </div>

            <div className="col-12 col-md-6 col-lg-3">

                <div className="card mb-3">
                    <div className="card-header">
                        <h5 className="m-0"><i className="fa fa-gift"/>&nbsp;{translator('order_types_busybee')}</h5>
                    </div>
                    <div className="card-body">

                        {!canManagebusybeeOrders ? <p className="c-red-500">
                            <i className="fa fa-warning"/>&nbsp;{translator('no_account_for_busybee')}
                        </p> : null}


                        {model.postalCodesbusybee.length > 0
                            ? <ul className="simple" style={cardStyle}>{model.postalCodesbusybee.map((item, i) =>
                                <li key={i}><i
                                    className={"fa " + (canManagebusybeeOrders ? "fa-check" : "fa-lock")}/>&nbsp;{item}
                                </li>)}
                            </ul>
                            : <span>{translator('no_assigned_postal_codes')}</span>}
                    </div>
                </div>

            </div>

        </div>
    }

    renderProfileForm = () => {

        const {model, isLoading} = this.props.ProfilePartner
        const {items} = this.props.ProfilePartner.Countries

        const isDemo = model.id && model.user && model.user.isDemo

        return <div className="row">
            <div className="col-12 col-md-3">

                <div className="img-container text-center">
                    {!isLoading && model.user && model.user.avatar
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

            <div className="col-12 col-md-6">

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

                    <div className="col-12">
                        <div className="form-group">
                            <label className="required">{translator('country')}</label>
                            <select
                                name="country"
                                className="form-control"
                                onChange={this.changeCountry}
                                value={model.country && model.country.id ? model.country.id : -1}>
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
                            <textarea name="address"
                                      className="form-control"
                                      onChange={this.changeString('address')}
                                      value={model.location ? model.location.address : ''}/>
                            {this.getError('address')}
                        </div>
                    </div>

                </div>

                <div className="row">
                    <div className="col-12 col-sm-4">

                        <div className="form-group">
                            <label className={!model.id ? "required" : ""}>{translator('current_password')}</label>
                            <input type="password"
                                   name="currentPassword"
                                   className="form-control"
                                   onChange={this.changeString('currentPassword')}
                                   disabled={isDemo}
                                   value={model.user.currentPassword || ''}/>
                            {this.getError('currentPassword')}
                        </div>

                    </div>
                    <div className="col-12 col-sm-4">

                        <div className="form-group">
                            <label className={!model.id ? "required" : ""}>{translator('password')}</label>
                            <input type="password"
                                   name="password"
                                   className="form-control"
                                   onChange={this.changeString('password')}
                                   disabled={isDemo}
                                   value={model.user.password || ''}/>
                            {this.getError('password')}
                        </div>

                    </div>
                    <div className="col-12 col-sm-4">

                        <div className="form-group">
                            <label
                                className={!model.id ? "required" : ""}>{translator('password_repeat')}</label>
                            <input type="password"
                                   name="password2"
                                   className="form-control"
                                   onChange={this.changeString('password2')}
                                   disabled={isDemo}
                                   value={model.user.password2 || ''}/>
                            {this.getError('password2')}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    }

    render() {

        const {model, isSaveSuccess, serverErrors, isLoading, isValid} = this.props.ProfilePartner

        const isModalOpen = this.props.ProfilePartner.RequestedCodes.isModalOpen
        const isCategoryModalOpen = this.props.ProfilePartner.RequestedCategories.isModalOpen

        return <div>

            <div className="card my-3">

                <div className="card-header">
                    <div className="row">
                        <div className="col-6">
                            <h4 className="m-0">{translator('navigation_profile')}</h4>
                        </div>
                        <div className="col-6 text-right">

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
                </div>

                <div className="card-body">

                    {serverErrors.length > 0 && <div className="alert alert-danger">
                        <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                    </div>}

                    <div className="row mb-4">
                        <div className="col-12">
                            {this.renderProfileForm()}
                        </div>
                    </div>

                </div>
            </div>


            <div className="row">
                <div className="col-12">
                    {this.renderAssignedPostalCodes()}
                </div>
            </div>


            <div className="row">
                <div className="col-12 col-md-6">
                    <div className="card mb-3">
                        <div className="card-header">
                            <div className="row">
                                <div className="col">
                                    <h4 className="m-0">{translator('requested_postal_codes')}</h4>
                                </div>
                                <div className="col-auto">
                                    <div className="text-left mb-2">
                                        <button type="button" className="btn btn-primary btn-sm"
                                                onClick={this.toggleModal}>
                                            <i className="fa fa-plus"/>&nbsp;{translator('add')}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="card-body">
                            {this.renderRequestedPostalCodes()}
                        </div>
                    </div>
                </div>

                <div className="col-12 col-md-6">
                    <div className="card mb-3">
                        <div className="card-header">
                            <div className="row">
                                <div className="col">
                                    <h4 className="m-0">{translator('requested_categories')}</h4>
                                </div>
                                <div className="col-auto">
                                    <div className="text-left mb-2">
                                        <button type="button" className="btn btn-primary btn-sm"
                                                onClick={this.toggleCategoryModal}>
                                            <i className="fa fa-plus"/>&nbsp;{translator('add')}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="card-body">
                            {this.renderRequestedCategories()}
                        </div>
                    </div>
                </div>
            </div>


            {AppParameters.payments.stripe.isEnabled && <PaymentInfo/>}

            {/*<PaymentInfoRecycling/>*/}

            {isModalOpen && <RequestedCodesModal/>}

            {isCategoryModalOpen && <RequestedCategoriesModal/>}
        </div>
    }
}

export default withRouter(connect(selectors)(ProfilePartner))
