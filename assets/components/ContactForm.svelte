<script>
    import FormInput from './FormInput.svelte'
    import FormButton from './FormButton.svelte'
    import FormCheckbox from './FormCheckbox.svelte'
    import {omit} from 'lodash-es'
    import {post} from '../utils/api'

    export let url
    export let errors = {}

    let rgpd = false
    let success = false
    let loading = false
    let data = {
        name: '',
        email: '',
        phone: '',
        message: ''
    }

    async function onSubmit(e) {
        e.preventDefault()
        errors = {}
        if (rgpd === false) {
            errors.rgpd = 'Your need to accept rgpd'
            return
        }
        loading = true
        try {
            await post(url, data)
            success = true
            data = {
                name: '',
                email: '',
                phone: '',
                message: '',
                rgpd: true,
            }
        } catch (e) {
            if (e.errors) {
                errors = e.errors
            } else {
                alert(e.message)
                console.error(e)
            }
        }
        loading = false
    }

    function clearError(name) {
        return function () {
            errors = omit(errors, name)
        }
    }
</script>

{#if success}
    <div class="alert alert-success">Your mail successfully sent.</div>
{/if}

<form method="post" on:submit={onSubmit}>
    <div class="row">
        <div class="col-md-6">
            <FormInput required error={errors.name} name="name" label="Your name" bind:value={data.name}/>
        </div>
        <div class="col-md-6">
            <FormInput required error={errors.email} name="email" label="Your email" type="email" bind:value={data.email}/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <FormInput required error={errors.phone} name="phone" label="Your phone number" type="phone"
                       bind:value={data.phone}/>
        </div>
    </div>
    <FormInput required error={errors.message} name="message" label="Your message" type="textarea"
               bind:value={data.message}/>
    <FormCheckbox name="rgpd" bind:value={rgpd} error={errors.rgpd} on:change={clearError('rgpd')} required>
        Please accept conditions
    </FormCheckbox>
    <FormButton disabled={loading}>Submit</FormButton>
</form>
