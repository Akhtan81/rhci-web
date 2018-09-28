import reducer from '../../../src/PartnerRegister/reducers/model'

describe('`PartnerRegister` model reducer', () => {

    it('`id` should return the initial state', () => {
        expect(reducer(undefined, {}).id).toEqual(null)
    })

    it('`createdAt` should return the initial state', () => {
        expect(reducer(undefined, {}).createdAt).toEqual(null)
    })

    it('`isAccepted` should return the initial state', () => {
        expect(reducer(undefined, {}).isAccepted).toEqual(false)
    })

    it('`requestedPostalCodes` should return the initial state', () => {
        const result = reducer(undefined, {}).requestedPostalCodes

        expect(typeof result).toEqual("object")

        const keys = Object.keys(result)

        expect(keys.length).toEqual(1)

        keys.forEach(key => {
            expect(result[key].postalCode).toEqual(null)
            expect(result[key].type).toEqual(null)
            expect(result[key].cid).toEqual(key)
        })
    })
})