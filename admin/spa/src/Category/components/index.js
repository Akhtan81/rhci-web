import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import selectors from './selectors';
import translator from '../../translations/translator';
import FetchItems from '../actions/FetchItems';
import {FILTER_CHANGED} from '../actions';
import {dateFormat, setTitle} from '../../Common/utils';

class Index extends React.Component {

    componentWillMount() {

        setTitle(translator('navigation_categories'))

        const {filter} = this.props.Category

        this.props.dispatch(FetchItems(filter))
    }

    setLocale = e => {
        this.props.dispatch({
            type: FILTER_CHANGED,
            payload: {
                locale: e.target.value
            }
        })
    }

    setFilterType = type => () => {
        this.props.dispatch({
            type: FILTER_CHANGED,
            payload: {type}
        })
    }

    render() {

        const {filter, isLoading} = this.props.Category

        return <div className="card my-3">

            <div className="card-header">
                <div className="row">
                    <div className="col">
                        <h4 className="m-0">{translator('navigation_categories')}</h4>
                    </div>
                    <div className="col text-right">

                        <Link to="/categories/new" className="btn btn-success btn-sm">
                            <i className="fa fa-plus"/>&nbsp;{translator('add')}
                        </Link>
                    </div>
                </div>
            </div>
            <div className="card-body">
                <div className="row">
                    <div className="col">
                        <ul className="nav nav-tabs mb-2">
                            <li className="nav-item">
                                <div
                                    className={"nav-link" + (filter && filter.type === 'junk_removal' ? ' active' : '')}
                                    onClick={this.setFilterType('junk_removal')}>

                                    {filter && filter.type === 'junk_removal' && isLoading
                                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                                        : <i className="fa fa-cubes"/>}

                                    &nbsp;{translator('order_types_junk_removal')}
                                </div>
                            </li>
                            <li className="nav-item">
                                <div className={"nav-link" + (filter && filter.type === 'recycling' ? ' active' : '')}
                                     onClick={this.setFilterType('recycling')}>

                                    {filter && filter.type === 'recycling' && isLoading
                                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                                        : <i className="fa fa-recycle"/>}

                                    &nbsp;{translator('order_types_recycling')}
                                </div>
                            </li>
                            <li className="nav-item">
                                <div className={"nav-link" + (filter && filter.type === 'donation' ? ' active' : '')}
                                     onClick={this.setFilterType('donation')}>

                                    {filter && filter.type === 'donation' && isLoading
                                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                                        : <i className="fa fa-gift"/>}

                                    &nbsp;{translator('order_types_donation')}
                                </div>
                            </li>
                            <li className="nav-item">
                                <div className={"nav-link" + (filter && filter.type === 'shredding' ? ' active' : '')}
                                     onClick={this.setFilterType('shredding')}>

                                    {filter && filter.type === 'shredding' && isLoading
                                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                                        : <i className="fa fa-stack-overflow"/>}

                                    &nbsp;{translator('order_types_shredding')}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                {this.renderItems()}
            </div>
        </div>
    }

    renderItems = () => {

        const {items, isLoading} = this.props.Category

        if (!isLoading && items.length === 0) {
            return <div className="banner">
                <h3>{translator('no_categories_title')}</h3>
                <h4>{translator('no_categories_footer')}</h4>
            </div>
        }

        return <div className="table-responsive mb-3">
            <table className="table table-sm table-hover">
                <thead>
                <tr>
                    <th className="text-left">{translator('name')}</th>
                    <th className="text-right">{translator('locale')}</th>
                    <th className="text-right">{translator('created_at')}</th>
                </tr>
                </thead>

                <tbody>{items.map(this.renderChild)}</tbody>
            </table>
        </div>
    }

    renderChild = (model, key) => {
        let prefix = ''
        for (let i = 0; i < model.lvl; i++) {
            prefix += '- '
        }
        return <tr key={key}>
            <td className="no-wrap">
                <Link to={'/categories/' + model.id}>{prefix}{model.name}</Link>
            </td>
            <td className="text-right text-nowrap">{model.locale}</td>
            <td className="text-right text-nowrap">{dateFormat(model.createdAt)}</td>
        </tr>
    }
}

export default withRouter(connect(selectors)(Index))
