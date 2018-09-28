import reducer from '../../../src/PartnerEdit/reducers/location'

describe('`PartnerEdit` location reducer', () => {

    it('`id` should return the initial state', () => {
        expect(reducer(undefined, {}).id).toEqual(null)
    })

    it('`postalCode` should return the initial state', () => {
        expect(reducer(undefined, {}).postalCode).toEqual(null)
    })

    it('`address` should return the initial state', () => {
        expect(reducer(undefined, {}).address).toEqual(null)
    })

    it('`lng` should return the initial state', () => {
        expect(reducer(undefined, {}).lng).toEqual(null)
    })

    it('`lat` should return the initial state', () => {
        expect(reducer(undefined, {}).lat).toEqual(null)
    })
})