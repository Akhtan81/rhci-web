import reducer from '../../../src/PartnerRegister/reducers/location'

describe('`PartnerRegister` location reducer', () => {

    it('`id` should return the initial state', () => {
        expect(reducer(undefined, {}).id).toEqual(null)
    })

    it('`postalCode` should return the initial state', () => {
        expect(reducer(undefined, {}).postalCode).toEqual(null)
    })

    it('`address` should return the initial state', () => {
        expect(reducer(undefined, {}).address).toEqual(null)
    })

})