import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import selectors from './selectors';
import translator from '../../translations/translator';
import FetchItems from '../actions/FetchItems';
import Paginator from '../../Common/components/Paginator';
import {FILTER_CHANGED, FILTER_CLEAR, PAGE_CHANGED} from '../actions';
import FetchCountries from "../actions/FetchCountries";

class Index extends React.Component {

    componentWillMount() {
        this.fetchItems()

        const {Country} = this.props.Partner
        if (!Country.isLoading && Country.items.length === 0) {
            this.props.dispatch(FetchCountries())
        }
    }

    fetchItems = () => {
        const {filter, page} = this.props.Partner

        this.props.dispatch(FetchItems(filter, page))
    }

    fetchItemsIfEnter = e => {
        switch (e.keyCode) {
            case 13:
                this.fetchItems()
        }
    }

    setPage = page => {
        this.props.dispatch({
            type: PAGE_CHANGED,
            payload: page
        })
    }

    changeSelect = name => e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value) || value < 0) value = null;

        this.change(name, value)
    }

    changeFilter = key => e => this.change(key, e.target.value)

    change = (key, value) => {
        this.props.dispatch({
            type: FILTER_CHANGED,
            payload: {
                [key]: value
            }
        })
    }

    clearFilter = () => {
        this.props.dispatch({
            type: FILTER_CLEAR,
            payload: {}
        })
    }

    render() {

        const {
            filter,
            page,
            limit,
            total,
            isLoading,
            Country,
            Region,
            City,
            District
        } = this.props.Partner

        return <div className="bgc-white bd bdrs-3 p-20 mB-20">


            <div className="row">
                <div className="col">
                    <h4 className="page-title">
                        {translator('navigation_partners')}
                    </h4>
                </div>
                <div className="col text-right">

                    <Link to="/partners/new" className="btn btn-success btn-sm">
                        <i className="fa fa-plus"/>&nbsp;{translator('add')}
                    </Link>
                </div>
            </div>

            <div className="row">
                <div className="col">
                    <div className="form-inline">
                        <div className="input-group input-group-sm mr-2 mb-2">
                            <input type="text" className="form-control"
                                   name="search"
                                   value={filter.search || ''}
                                   placeholder={translator('search_placeholder')}
                                   onKeyDown={this.fetchItemsIfEnter}
                                   onChange={this.changeFilter('search')}/>
                        </div>
                        <div className="input-group input-group-sm mr-2 mb-2">
                            <select name="country" className="form-control"
                                    value={filter.country || -1}
                                    onChange={this.changeSelect('country')}>
                                <option value={-1}>{translator('select_country')}</option>
                                {Country.items.map((item, i) => <option key={i} value={item.id}>{item.name}</option>)}
                            </select>
                        </div>
                        <div className="input-group input-group-sm mr-2 mb-2">
                            <select name="region" className="form-control"
                                    disabled={!filter.country}
                                    value={filter.region || -1}
                                    onChange={this.changeSelect('region')}>
                                <option value={-1}>{translator('select_region')}</option>
                                {Region.items.map((item, i) => <option key={i} value={item.id}>{item.name}</option>)}
                            </select>
                        </div>
                        <div className="input-group input-group-sm mr-2 mb-2">
                            <select name="city" className="form-control"
                                    disabled={!filter.region}
                                    value={filter.city || -1}
                                    onChange={this.changeSelect('city')}>
                                <option value={-1}>{translator('select_city')}</option>
                                {City.items.map((item, i) => <option key={i} value={item.id}>{item.name}</option>)}
                            </select>
                        </div>
                        <div className="input-group input-group-sm mr-2 mb-2">
                            <select name="district" className="form-control"
                                    disabled={!filter.city}
                                    value={filter.district || -1}
                                    onChange={this.changeSelect('district')}>
                                <option value={-1}>{translator('select_district')}</option>
                                {District.items.map((item, i) => <option key={i} value={item.id}>{item.postalCode + " | " + item.name}</option>)}
                            </select>
                        </div>
                        <div className="input-group input-group-sm mr-2 mb-2">
                            <select name="district" className="form-control"
                                    value={filter.isActive}
                                    onChange={this.changeSelect('isActive')}>
                                <option value={1}>{translator('active')}</option>
                                <option value={0}>{translator('inactive')}</option>
                            </select>
                        </div>

                        <button className="btn btn-sm btn-primary mr-2 mb-2"
                                disabled={isLoading}
                                onClick={this.fetchItems}>
                            <i className={"fa " + (isLoading ? "fa-spin fa-circle-o-notch" : "fa-search")}/>&nbsp;{translator('search')}
                        </button>

                        <button className="btn btn-sm btn-default mr-2 mb-2"
                                disabled={isLoading}
                                onClick={this.clearFilter}>
                            <i className="fa fa-times"/>&nbsp;{translator('clear')}
                        </button>

                    </div>
                </div>
            </div>

            <div className="row">
                <div className="col">
                    {this.renderItems()}
                </div>
            </div>

            <div className="row">
                <div className="col text-center">
                    <Paginator
                        limit={limit}
                        page={page}
                        total={total}
                        onChange={this.setPage}/>
                </div>
            </div>
        </div>
    }

    renderItems = () => {

        const {items, isLoading} = this.props.Partner

        if (!isLoading && items.length === 0) {
            return <div className="banner">
                <h3>{translator('no_partners_title')}</h3>
                <h4>{translator('no_partners_footer')}</h4>
            </div>
        }

        return <div className="table-responsive mb-3">
            <table className="table table-sm table-hover">
                <thead>
                <tr>
                    <th>{translator('name')}</th>
                    <th>{translator('email')}</th>
                    <th>{translator('phone')}</th>
                    <th>{translator('is_active')}</th>
                    <th>{translator('district')}</th>
                </tr>
                </thead>

                <tbody>{items.map(this.renderChild)}</tbody>
            </table>
        </div>
    }

    renderChild = (model, key) => {
        return <tr key={key}>
            <td className="text-nowrap">
                <Link to={'/partners/' + model.id}>{model.user.name}</Link>
            </td>
            <td className="text-nowrap">{model.user.email || ''}</td>
            <td className="text-nowrap">{model.user.phone || ''}</td>
            <td className="text-nowrap">
                {model.user.isActive
                    ? <div className="badge badge-pill badge-success">
                        <i className='fa fa-check'/>&nbsp;{translator('active')}
                    </div>
                    : <div className="badge badge-pill badge-danger">
                        <i className='fa fa-ban'/>&nbsp;{translator('inactive')}
                    </div>}
            </td>
            <td className="text-nowrap">
                <Link to={'/districts/' + model.district.id}>{model.district.postalCode + " | " + model.district.fullName}</Link>
            </td>
        </tr>
    }
}

export default withRouter(connect(selectors)(Index))
